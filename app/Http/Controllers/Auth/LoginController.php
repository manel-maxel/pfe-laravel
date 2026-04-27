<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    public function redirectToKeycloak()
    {
        return Socialite::driver('keycloak')->redirect();
    }

    public function handleKeycloakCallback()
    {
        try {
            $keycloakUser = Socialite::driver('keycloak')->user();

            $idToken = $keycloakUser->accessTokenResponseBody['id_token'] ?? null;
            session(['id_token_hint' => $idToken]);

            $accessToken = $keycloakUser->accessTokenResponseBody['access_token'] ?? null;

            // decode JWT
            $payload = [];
            if ($accessToken) {
                $parts = explode('.', $accessToken);
                if (count($parts) === 3) {
                    $payload = json_decode(base64_decode($parts[1]), true) ?? [];
                }
            }

            // CLIENT ID de CETTE application
            $currentClientId = env('KEYCLOAK_CLIENT_ID');
            
            // NE prendre que les rôles de CE client
            $clientRoles = $payload['resource_access'][$currentClientId]['roles'] ?? [];
            
            // Déterminer le rôle
            $userRole = 'null';
            if (in_array('admin', $clientRoles)) {
                $userRole = 'admin';
            } elseif (in_array('manager', $clientRoles)) {
                $userRole = 'manager';
            } elseif (in_array('employee', $clientRoles)) {
                $userRole = 'employee';
            }

            // Récupération des infos utilisateur
            $username = $keycloakUser->user['preferred_username'] ?? 
                       $keycloakUser->user['username'] ?? 
                       explode('@', $keycloakUser->user['email'])[0];
            
            $firstName = $keycloakUser->user['firstName'] ?? null;
            $lastName  = $keycloakUser->user['lastName'] ?? null;

            if (empty($firstName) || empty($lastName)) {
                $nameParts = explode(' ', $keycloakUser->user['name'] ?? '');
                $firstName = $firstName ?? ($nameParts[0] ?? 'User');
                $lastName  = $lastName ?? ($nameParts[1] ?? '');
            }

            $user = User::updateOrCreate(
                ['email' => $keycloakUser->user['email']],
                [
                    'username'    => $username,
                    'first_name'  => $firstName,
                    'last_name'   => $lastName,
                    'keycloak_id' => $keycloakUser->user['sub'],
                    'role'        => $userRole,
                    'password'    => bcrypt(Str::random(24)),
                ]
            );

            Auth::login($user);

            if ($userRole === 'admin') {
                return redirect('/admin/dashboard');
            } elseif ($userRole === 'manager') {
                return redirect('/manager/dashboard');
            } elseif ($userRole === 'employee') {
                return redirect('/employee/dashboard');
            }else{
                Auth::logout();
                return redirect('/login')->with('error', 'Aucun rôle valide trouvé pour cet utilisateur.');
            }

        } catch (\Exception $e) {
            return redirect('/login')
                ->with('error', 'Erreur d\'authentification: ' . $e->getMessage());
        }
    }

    public function logout()
    {
        $idToken = session('id_token_hint');
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        $keycloakLogoutUrl = env('KEYCLOAK_BASE_URL') 
            . '/realms/' . env('KEYCLOAK_REALM') 
            . '/protocol/openid-connect/logout';

        $logoutUrl = $keycloakLogoutUrl . '?post_logout_redirect_uri=' . urlencode(url('/login'));
        
        if ($idToken) {
            $logoutUrl .= '&id_token_hint=' . $idToken;
        }
        
        return redirect($logoutUrl);
    }
}
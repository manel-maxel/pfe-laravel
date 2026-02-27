<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function redirectToKeycloak()
    {
        return Socialite::driver('keycloak')->redirect();
    }

    public function handleKeycloakCallback(Request $request)
    {
        try {
            if ($request->has('error')) {
                return redirect('/')->with('error', 'Connexion annulée');
            }

            $keycloakUser = Socialite::driver('keycloak')->user();
            
            // Chercher l'utilisateur par email
            $user = User::where('email', $keycloakUser->getEmail())->first();
            
            if (!$user) {
                // Créer l'utilisateur s'il n'existe pas
                $user = User::create([
                    'name' => $keycloakUser->getName() ?? $keycloakUser->getEmail(),
                    'email' => $keycloakUser->getEmail(),
                    'password' => bcrypt(Str::random(24)),
                ]);
            }
            
            // Connecter l'utilisateur dans Laravel
            Auth::login($user, true);
            
            return redirect()->intended('/dashboard')->with('success', 'Connexion réussie !');
            
        } catch (\Exception $e) {
            Log::error('Keycloak callback error: ' . $e->getMessage());
            return redirect('/')->with('error', 'Erreur de connexion : ' . $e->getMessage());
        }
    }

    public function logout()
    {
        Auth::logout();
        
        // Déconnexion de Keycloak
        $logoutUrl = 'http://localhost:8080/realms/AlgerieTelecom/protocol/openid-connect/logout?redirect_uri=' . urlencode('http://localhost:8000');
        
        return redirect($logoutUrl);
    }
}
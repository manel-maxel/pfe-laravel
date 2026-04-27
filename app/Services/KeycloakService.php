<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class KeycloakService
{
    private string $baseUrl;
    private string $realm;
    private string $adminClientId;
    private string $adminClientSecret;
    private string $appClientId;

    public function __construct()
    {
        $this->baseUrl           = env('KEYCLOAK_BASE_URL', 'http://localhost:8080');
        $this->realm             = env('KEYCLOAK_REALM', 'AlgerieTelecom');
        $this->adminClientId     = env('KEYCLOAK_ADMIN_CLIENT_ID', 'laravel-admin-cli');
        $this->adminClientSecret = env('KEYCLOAK_ADMIN_CLIENT_SECRET');
        $this->appClientId       = env('KEYCLOAK_CLIENT_ID', 'laravel-app');
    }

    public function getAdminToken(): string
    {
        return Cache::remember('keycloak_admin_token', 50, function () {
            $response = Http::asForm()->post(
                "{$this->baseUrl}/realms/{$this->realm}/protocol/openid-connect/token",
                [
                    'grant_type'    => 'client_credentials',
                    'client_id'     => $this->adminClientId,
                    'client_secret' => $this->adminClientSecret,
                ]
            );

            $data = $response->json();

            if (!isset($data['access_token'])) {
                throw new \Exception('Keycloak admin token error: ' . json_encode($data));
            }

            return $data['access_token'];
        });
    }

    public function getAppClientUuid(): string
    {
        return Cache::remember('keycloak_client_uuid', 300, function () {
            $token = $this->getAdminToken();

            $clients = Http::withToken($token)
                ->get("{$this->baseUrl}/admin/realms/{$this->realm}/clients", [
                    'clientId' => $this->appClientId
                ])->json();

            if (empty($clients) || !isset($clients[0]['id'])) {
                throw new \Exception("Client '{$this->appClientId}' introuvable.");
            }

            return $clients[0]['id'];
        });
    }

    public function getUsersWithRoles(): array
    {
        $token      = $this->getAdminToken();
        $clientUuid = $this->getAppClientUuid();

        $users = Http::withToken($token)
            ->get("{$this->baseUrl}/admin/realms/{$this->realm}/users")
            ->json();

        foreach ($users as &$user) {
            $roles = Http::withToken($token)
                ->get("{$this->baseUrl}/admin/realms/{$this->realm}/users/{$user['id']}/role-mappings/clients/{$clientUuid}")
                ->json();

            $user['roles'] = array_values(array_filter(
                array_map(fn($r) => $r['name'], $roles ?? []),
                fn($name) => in_array($name, ['admin', 'manager', 'employee'])
            ));

            $user['role'] = $user['roles'][0] ?? 'N/A';
        }

        return $users;
    }

    public function createUser(array $data): string
    {
        $token = $this->getAdminToken();

        $response = Http::withToken($token)
            ->post("{$this->baseUrl}/admin/realms/{$this->realm}/users", [
                'username'    => $data['username'],
                'email'       => $data['email'],
                'firstName'   => $data['firstName'] ?? '',
                'lastName'    => $data['lastName'] ?? '',
                'enabled'     => true,
                'credentials' => [[
                    'type'      => 'password',
                    'value'     => $data['password'],
                    'temporary' => false,
                ]],
            ]);

        if ($response->status() !== 201) {
            throw new \Exception('Erreur création Keycloak : ' . $response->body());
        }

        $keycloakId = basename($response->header('Location'));

        $this->assignClientRole($keycloakId, $data['role'] ?? 'employee');

        return $keycloakId;
    }

    public function deleteUser(string $userId): void
    {
        $token = $this->getAdminToken();

        $response = Http::withToken($token)
            ->delete("{$this->baseUrl}/admin/realms/{$this->realm}/users/{$userId}");

        if ($response->failed()) {
            throw new \Exception('Erreur suppression Keycloak : ' . $response->body());
        }
    }

    public function assignClientRole(string $userId, string $roleName): void
    {
        $token      = $this->getAdminToken();
        $clientUuid = $this->getAppClientUuid();

        $roleResponse = Http::withToken($token)
            ->get("{$this->baseUrl}/admin/realms/{$this->realm}/clients/{$clientUuid}/roles/{$roleName}");

        if ($roleResponse->failed()) {
            throw new \Exception("Rôle '{$roleName}' introuvable.");
        }

        $role = $roleResponse->json();

        Http::withToken($token)
            ->post(
                "{$this->baseUrl}/admin/realms/{$this->realm}/users/{$userId}/role-mappings/clients/{$clientUuid}",
                [['id' => $role['id'], 'name' => $role['name']]]
            );
    }

    public function updateClientRole(string $userId, string $newRole): void
    {
        $token      = $this->getAdminToken();
        $clientUuid = $this->getAppClientUuid();

        $currentRoles = Http::withToken($token)
            ->get("{$this->baseUrl}/admin/realms/{$this->realm}/users/{$userId}/role-mappings/clients/{$clientUuid}")
            ->json() ?? [];

        $rolesToRemove = array_values(array_filter(
            $currentRoles,
            fn($r) => in_array($r['name'], ['admin', 'manager', 'employee'])
        ));

        if (!empty($rolesToRemove)) {
            Http::withToken($token)->delete(
                "{$this->baseUrl}/admin/realms/{$this->realm}/users/{$userId}/role-mappings/clients/{$clientUuid}",
                $rolesToRemove
            );
        }

        $this->assignClientRole($userId, $newRole);
    }

    public function updateUser(string $userId, array $data): void
    {
        $token = $this->getAdminToken();

        $userData = [
            'email'     => $data['email'] ?? null,
            'enabled'   => true,
        ];

        if (isset($data['firstName'])) {
            $userData['firstName'] = $data['firstName'];
        }
        if (isset($data['lastName'])) {
            $userData['lastName'] = $data['lastName'];
        }
        if (isset($data['username'])) {
            $userData['username'] = $data['username'];
        }

        $response = Http::withToken($token)
            ->put("{$this->baseUrl}/admin/realms/{$this->realm}/users/{$userId}", $userData);

        if ($response->failed()) {
            throw new \Exception('Erreur mise à jour Keycloak : ' . $response->body());
        }
    }
}

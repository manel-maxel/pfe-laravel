<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/auth/redirect', [App\Http\Controllers\Auth\LoginController::class, 'redirectToKeycloak'])
    ->name('keycloak.login');

Route::get('/auth/callback', [App\Http\Controllers\Auth\LoginController::class, 'handleKeycloakCallback'])
    ->name('keycloak.callback');

Route::get('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])
    ->name('logout');

Route::get('/test-config', function () {
    return [
        'keycloak_base_url' => config('services.keycloak.base_url'),
        'keycloak_realm' => config('services.keycloak.realm'), 
        'keycloak_client_id' => config('services.keycloak.client_id'),
        'keycloak_redirect' => config('services.keycloak.redirect'),
        'client_secret_set' => !empty(config('services.keycloak.client_secret')),
    ];
});

// Route protégée (test)
/*
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');
*/
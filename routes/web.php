<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Manager\ManagerController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\Auth\LoginController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Auth routes
Route::get('/login', [LoginController::class, 'redirectToKeycloak'])->name('login');
Route::get('/auth/callback', [LoginController::class, 'handleKeycloakCallback']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware(['auth'])->group(function () {

    // Admin routes
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {

        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/users/create', [AdminController::class, 'create'])->name('users.create');
        Route::post('/users/store', [AdminController::class, 'store'])->name('users.store');
        Route::post('/users/{id}/role', [AdminController::class, 'updateRole'])->name('users.updateRole');
        Route::delete('/users/{id}', [AdminController::class, 'destroy'])->name('users.destroy');

        // Routes pour le profil (avec le préfixe admin/)
        Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
        Route::get('/edit-profile', [AdminController::class, 'editProfile'])->name('edit-profile');
        Route::put('/profile/update', [AdminController::class, 'updateProfile'])->name('profile.update');
    });

    // Manager routes
    Route::middleware(['role:manager'])->group(function () {
        Route::get('/manager/dashboard', [ManagerController::class, 'dashboard'])->name('manager.dashboard');
        Route::get('/manager/reports', [ManagerController::class, 'reports'])->name('manager.reports');
        Route::post('/manager/reports/{id}/validate', [ManagerController::class, 'validateReport'])->name('manager.reports.validate');
        Route::post('/manager/reports/{id}/reject', [ManagerController::class, 'rejectReport'])->name('manager.reports.reject');
        Route::delete('/manager/tasks/{task}', [ManagerController::class, 'destroy'])->name('manager.task.destroy');
        Route::get('/profile', [ManagerController::class, 'profile'])->name('manager.profile');
        Route::get('/edit-profile', [ManagerController::class, 'editProfile'])->name('manager.edit-profile');
        Route::put('/profile/update', [ManagerController::class, 'updateProfile'])->name('manager.profile.update');
    });

    // Employee routes (simplifiées)
    Route::middleware(['role:employee'])->prefix('employee')->group(function () {
        Route::get('/dashboard', [EmployeeController::class, 'dashboard'])->name('employee.dashboard');
        Route::get('/profile', [EmployeeController::class, 'profile'])->name('employee.profile');
        Route::get('/edit-profile', [EmployeeController::class, 'editProfile'])->name('employee.edit-profile');
        Route::put('/profile/update', [EmployeeController::class, 'updateProfile'])->name('employee.profile.update');

        Route::get('/reports/create', [EmployeeController::class, 'createReport'])->name('employee.reports.create');
        Route::post('/reports/store', [EmployeeController::class, 'storeReport'])->name('employee.reports.store');
    });
});

// Debug API (accessible à tous les rôles authentifiés)
Route::get('/api/my-info', function () {
    if (!auth()->check()) {
        return response()->json(['error' => 'Non authentifié'], 401);
    }

    return response()->json([
        'username' => auth()->user()->username ?? auth()->user()->email,
        'email' => auth()->user()->email,
        'role' => auth()->user()->role,
        'user_id' => auth()->user()->id,
        'session_id' => session()->getId(),
    ]);
});

// Debug route (sans auth)
Route::get('/test-config', function () {
    return [
        'keycloak_base_url' => config('services.keycloak.base_url'),
        'keycloak_realm' => config('services.keycloak.realm'),
        'keycloak_client_id' => config('services.keycloak.client_id'),
        'keycloak_redirect' => config('services.keycloak.redirect'),
        'client_secret_set' => !empty(config('services.keycloak.client_secret')),
    ];
});

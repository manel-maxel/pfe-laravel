<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\KeycloakService;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    private KeycloakService $keycloak;

    public function __construct(KeycloakService $keycloak)
    {
        $this->keycloak = $keycloak;
    }

    public function dashboard()
    {
        $users = $this->keycloak->getUsersWithRoles();

        $totalUsers = count($users);
        $admins     = collect($users)->where('role', 'admin')->count();
        $employees  = collect($users)->where('role', 'employee')->count();

        $recentUsers = $users;

        return view('admin.dashboard', compact('recentUsers', 'totalUsers', 'admins', 'employees'));
    }

    public function users()
    {
        $users = $this->keycloak->getUsersWithRoles();
        return view('admin.users', compact('users'));
    }

    public function profile()
    {
        return view('admin/profile');
    }

    public function editProfile()
    {
        return view('admin.edit-profile');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        try {
            $this->keycloak->updateUser($user->keycloak_id, [
                'firstName' => $request->first_name,
                'lastName'  => $request->last_name,
                'email'     => $request->email,
            ]);

            $user->first_name = $request->first_name;
            $user->last_name  = $request->last_name;
            $user->email      = $request->email;
            $user->save();

            return redirect()->route('admin.profile')->with('success', 'Profile updated successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
    public function create()
    {
        return view('admin.create');
    }

    public function store(Request $request)
    {
        try {
            $keycloakId = $this->keycloak->createUser([
                'username'  => $request->username,
                'email'     => $request->email,
                'password'  => $request->password,
                'role'      => $request->role,
                'firstName' => $request->first_name,
                'lastName'  => $request->last_name,
            ]);

            User::create([
                'username'    => $request->username,
                'first_name'  => $request->first_name,
                'last_name'   => $request->last_name,
                'email'       => $request->email,
                'password'    => bcrypt($request->password),
                'role'        => $request->role,
                'keycloak_id' => $keycloakId,
            ]);

            return redirect('/admin/users')->with('success', 'User successfully created.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function updateRole(Request $request, $userId)
    {
        $request->validate([
            'role' => 'required|in:admin,manager,employee'
        ]);

        try {
            $this->keycloak->updateClientRole($userId, $request->role);

            $user = User::where('keycloak_id', $userId)->first();
            if ($user) {
                $user->role = $request->role;
                $user->save();
            }

            return back()->with('success', 'Role updated successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        try {
            $this->keycloak->deleteUser($id);
            User::where('keycloak_id', $id)->delete();

            return redirect('/admin/users')->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
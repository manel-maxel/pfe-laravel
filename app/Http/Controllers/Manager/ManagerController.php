<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Services\KeycloakService;
use Illuminate\Support\Facades\Auth;

class ManagerController extends Controller
{
     private KeycloakService $keycloak;

    public function __construct(KeycloakService $keycloak)
    {
        $this->keycloak = $keycloak;
    }

    public function dashboard()
    {
        $allUsers = $this->keycloak->getUsersWithRoles();
        $employees = collect($allUsers)->where('role', 'employee')->values();
        
        $pendingReports = Task::where('status', 'pending')->count();
        $approvedReports = Task::where('status', 'approved')->count();
        $activeTasks = Task::where('status', 'pending')->count();

        return view('manager.dashboard', [
            'users' => $employees,
            'employees' => $employees->count(),
            'pendingReports' => $pendingReports,
            'approvedReports' => $approvedReports,
            'activeTasks' => $activeTasks,
        ]);
    }
    public function profile()
    {
        return view('manager.profile');
    }
public function editProfile()
    {
        return view('manager.edit-profile');
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

            return redirect()->route('employee.profile')->with('success', 'Profile updated successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
    public function reports()
    {
        $tasks = Task::with('user')->orderBy('created_at', 'desc')->get();
        
        $pendingReports = Task::where('status', 'pending')->count();
        $approvedReports = Task::where('status', 'approved')->count();
        
        return view('manager.reports', compact('tasks', 'pendingReports', 'approvedReports'));
    }

    public function validateReport($id)
    {
        $task = Task::findOrFail($id);
        $task->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Report approved successfully');
    }

    public function rejectReport($id)
    {
        $task = Task::findOrFail($id);
        $task->update(['status' => 'rejected']);

        return back()->with('success', 'Report rejected');
    }
}
<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\KeycloakService;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    private KeycloakService $keycloak;

    public function __construct(KeycloakService $keycloak)
    {
        $this->keycloak = $keycloak;
    }

    public function dashboard()
    {
        $user = Auth::user();
        $tasks = Task::where('user_id', $user->id)->get();

        $completedTasks = $tasks->where('status', 'approved')->count();
        $pendingTasks   = $tasks->where('status', 'pending')->count();
        $totalTasks     = $tasks->count();
        $recentTasks    = $tasks->take(5);

        return view('employee.dashboard', compact(
            'completedTasks',
            'pendingTasks',
            'totalTasks',
            'recentTasks'
        ));
    }

    public function createReport()
    {
        return view('employee.create-report');
    }

    public function storeReport(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'report_date' => 'required|date',
        ]);

        Task::create([
            'user_id'     => Auth::id(),
            'title'       => $request->title,
            'description' => $request->description,
            'report_date' => $request->report_date,
            'status'      => 'pending',
        ]);

        return redirect()->route('employee.dashboard')->with('success', 'Report submitted successfully!');
    }

    public function profile()
    {
        return view('employee.profile');
    }

    public function editProfile()
    {
        return view('employee.edit-profile');
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
}
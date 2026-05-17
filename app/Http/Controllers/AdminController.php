<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Group;
use App\Models\Project;
use App\Models\Task;

class AdminController extends Controller
{
    public function dashboard()
    {
        $userCount = User::count();
        $groupCount = Group::count();
        $projectCount = Project::count();
        $taskCount = Task::count();
        
        return view('dashboard', compact('userCount', 'groupCount', 'projectCount', 'taskCount'));
    }

    public function users()
    {
        $users = User::with(['roles', 'members.group'])->paginate(10);
        $totalUsers = User::count();
        $students = User::role('student')->count();
        $supervisors = User::role('supervisor')->count();
        $admins = User::role('admin')->count();
        
        return view('admin.index', compact('users', 'totalUsers', 'students', 'supervisors', 'admins'));
    }

    public function search(Request $request)
    {
        $query = User::with(['roles', 'members.group']);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('registration_number', 'LIKE', "%{$search}%");
            });
        }

        if ($request->has('role') && $request->input('role') !== 'all') {
            $query->role($request->input('role'));
        }

        if ($request->has('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        $users = $query->paginate(10);

        return response()->json([
            'html' => view('admin._users_table', compact('users'))->render(),
            'pagination' => (string) $users->links()
        ]);
    }

    public function showUser(User $user)
    {
        $user->load(['roles', 'members.group', 'studentProfile', 'supervisorProfile', 'studentSkillsSurvey']);
        return response()->json($user);
    }
}

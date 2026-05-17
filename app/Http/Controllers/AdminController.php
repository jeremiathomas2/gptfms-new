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

    public function showUser(User $user)
    {
        $user->load(['roles', 'members.group', 'studentProfile', 'supervisorProfile', 'studentSkillsSurvey']);
        return response()->json($user);
    }
}

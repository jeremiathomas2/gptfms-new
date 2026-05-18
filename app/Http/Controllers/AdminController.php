<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Group;
use App\Models\GroupMember;
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

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'gender' => 'required|in:male,female,other',
            'role' => 'required|in:student,supervisor,admin',
            'registration_number' => 'nullable|string|max:50|unique:users,registration_number',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'],
            'last_name' => $validated['last_name'],
            'name' => trim($validated['first_name'] . ' ' . ($validated['middle_name'] ? $validated['middle_name'] . ' ' : '') . $validated['last_name']),
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'gender' => $validated['gender'],
            'registration_number' => $validated['registration_number'],
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
            'status' => 'active',
        ]);

        $user->assignRole($validated['role']);

        return response()->json(['success' => true, 'message' => 'User created successfully']);
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
        
        // Add surveyed skills if they exist
        $surveyedSkills = [];
        if ($user->studentSkillsSurvey) {
            $responses = json_decode($user->studentSkillsSurvey->responses, true) ?: [];
            foreach ($responses as $category => $skills) {
                if (is_array($skills)) {
                    foreach ($skills as $skill => $level) {
                        if ($level && $level !== 'none') {
                            $surveyedSkills[] = [
                                'name' => str_replace('_', ' ', $skill),
                                'level' => $level
                            ];
                        }
                    }
                }
            }
        }
        $user->surveyed_skills = $surveyedSkills;
        
        return response()->json($user);
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'registration_number' => 'nullable|string|max:50|unique:users,registration_number,' . $user->id,
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $user->update([
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'],
            'last_name' => $validated['last_name'],
            'name' => trim($validated['first_name'] . ' ' . ($validated['middle_name'] ? $validated['middle_name'] . ' ' : '') . $validated['last_name']),
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'gender' => $validated['gender'],
            'registration_number' => $validated['registration_number'],
            'status' => $validated['status'],
        ]);

        return response()->json(['success' => true, 'message' => 'User updated successfully']);
    }

    public function deleteUser(User $user)
    {
        // Prevent deleting yourself
        if (auth()->id() === $user->id) {
            return response()->json(['success' => false, 'message' => 'You cannot delete your own account.'], 403);
        }

        try {
            // Delete related profiles and survey if they exist (though cascade should handle this if set)
            // But manually handling for safety or if no FK cascade is set
            $user->delete();
            return response()->json(['success' => true, 'message' => 'User deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete user: ' . $e->getMessage()], 500);
        }
    }

    public function resetPassword(User $user)
    {
        try {
            $newPassword = \Illuminate\Support\Str::random(10);
            $user->update([
                'password' => \Illuminate\Support\Facades\Hash::make($newPassword)
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Password reset successfully',
                'new_password' => $newPassword
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to reset password: ' . $e->getMessage()], 500);
        }
    }

    /* ═══════════════════════════════════════════
       GROUP MANAGEMENT
    ═══════════════════════════════════════════ */

    public function groups()
    {
        $groups = Group::with(['members.user', 'project', 'supervisor'])->paginate(10);
        $projects = Project::all();
        $supervisors = User::role('supervisor')->get();
        $students = User::role('student')->get();
        
        return view('admin.groups', compact('groups', 'projects', 'supervisors', 'students'));
    }

    public function searchGroups(Request $request)
    {
        $query = Group::with(['members.user', 'project', 'supervisor']);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'LIKE', "%{$search}%");
        }

        $groups = $query->paginate(10);

        return response()->json([
            'html' => view('admin._groups_table', compact('groups'))->render(),
            'pagination' => (string) $groups->links()
        ]);
    }

    public function updateGroup(Request $request, Group $group)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'project_id' => 'nullable|exists:projects,id',
            'max_members' => 'required|integer|min:2|max:20',
            'status' => 'required|in:active,inactive',
        ]);

        $group->update($validated);

        return response()->json(['success' => true, 'message' => 'Group updated successfully']);
    }

    public function deleteGroup(Group $group)
    {
        try {
            $group->delete();
            return response()->json(['success' => true, 'message' => 'Group deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete group: ' . $e->getMessage()], 500);
        }
    }

    public function addGroupMember(Request $request, Group $group)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:leader,member',
        ]);

        // Check if user is already in any group
        if (GroupMember::where('user_id', $validated['user_id'])->where('status', 'joined')->exists()) {
            return response()->json(['success' => false, 'message' => 'User is already a member of a group.'], 422);
        }

        // Check if group is full
        if ($group->members->count() >= $group->max_members) {
            return response()->json(['success' => false, 'message' => 'Group is already full.'], 422);
        }

        GroupMember::create([
            'group_id' => $group->id,
            'user_id' => $validated['user_id'],
            'role' => $validated['role'],
            'status' => 'joined',
            'joined_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Member added successfully']);
    }

    public function removeGroupMember(GroupMember $member)
    {
        try {
            $member->delete();
            return response()->json(['success' => true, 'message' => 'Member removed successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to remove member.'], 500);
        }
    }

    public function assignSupervisor(Request $request, Group $group)
    {
        $validated = $request->validate([
            'supervisor_id' => 'required|exists:users,id',
        ]);

        $group->update([
            'supervisor_id' => $validated['supervisor_id']
        ]);

        return response()->json(['success' => true, 'message' => 'Supervisor assigned successfully']);
    }
}

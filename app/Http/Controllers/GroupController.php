<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\GroupMember;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Group::with(['members.user', 'project'])->paginate(9);
        return view('groups.index', compact('groups'));
    }

    public function myGroup()
    {
        $user = Auth::user();
        
        if ($user->hasRole('supervisor')) {
            $groups = Group::where('supervisor_id', $user->id)->with(['members.user', 'project'])->paginate(9);
            return view('supervisor.groups', compact('groups'));
        }

        $member = GroupMember::where('user_id', $user->id)
            ->where('status', 'joined')
            ->first();

        $group = $member ? Group::with(['members.user', 'project', 'supervisor'])->find($member->group_id) : null;
        
        return view('student.my_group', compact('group'));
    }

    public function show(Group $group)
    {
        $group->load(['members.user.studentSkillsSurvey', 'project', 'creator', 'supervisor']);
        
        // Add more context if needed, like project description or member specific details
        return response()->json([
            'id' => $group->id,
            'name' => $group->name,
            'status' => $group->status,
            'max_members' => $group->max_members,
            'description' => $group->description,
            'created_at' => $group->created_at->format('M d, Y'),
            'project' => $group->project ? [
                'title' => $group->project->title,
                'course_code' => $group->project->course_code,
                'description' => $group->project->description,
            ] : null,
            'supervisor' => $group->supervisor ? [
                'name' => $group->supervisor->name,
                'email' => $group->supervisor->email,
                'phone' => $group->supervisor->phone,
                'avatar' => $group->supervisor->avatar,
                'initials' => $group->supervisor->initials,
            ] : null,
            'members' => $group->members->map(function($member) {
                $skills = [];
                if ($member->user && $member->user->studentSkillsSurvey) {
                    $skills = $member->user->studentSkillsSurvey->skills ?: [];
                }
                
                return [
                    'role' => $member->role,
                    'user' => $member->user ? [
                        'name' => $member->user->name,
                        'email' => $member->user->email,
                        'phone' => $member->user->phone,
                        'avatar' => $member->user->avatar,
                        'initials' => $member->user->initials,
                        'registration_number' => $member->user->registration_number,
                        'skills' => $skills,
                    ] : null
                ];
            }),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
            'max_members' => 'required|integer|min:2|max:10',
            'description' => 'nullable|string',
        ]);

        $user = Auth::user();

        // Check if user is already in a group
        if (GroupMember::where('user_id', $user->id)->where('status', 'joined')->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'You are already a member of a group.'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $group = Group::create([
                'name' => $validated['name'],
                'project_id' => $validated['project_id'],
                'max_members' => $validated['max_members'],
                'description' => $validated['description'],
                'created_by' => Auth::id(),
                'status' => 'active',
            ]);

            // Automatically add the creator as a leader
            GroupMember::create([
                'group_id' => $group->id,
                'user_id' => Auth::id(),
                'role' => 'leader',
                'status' => 'joined',
                'joined_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Group created successfully!',
                'group' => $group
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error creating group: ' . $e->getMessage()
            ], 500);
        }
    }
}

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

    public function show(Group $group)
    {
        $group->load(['members.user', 'project', 'creator', 'supervisor']);
        return response()->json($group);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
            'max_members' => 'required|integer|min:2|max:10',
            'description' => 'nullable|string',
        ]);

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

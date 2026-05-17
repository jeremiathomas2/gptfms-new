<?php

namespace App\Http\Controllers;

use App\Models\GroupSetting;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\User;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GroupSettingsController extends Controller
{
    public function index()
    {
        $settings = GroupSetting::first() ?? new GroupSetting();
        return view('groups.settings', compact('settings'));
    }

    public function startCountdown(Request $request)
    {
        $settings = GroupSetting::first();
        if (!$settings) {
            $settings = new GroupSetting();
            $settings->created_by = Auth::id();
        }

        $settings->is_active = true;
        $settings->countdown_end_time = now()->addMinutes($settings->countdown_minutes ?? 60);
        $settings->updated_by = Auth::id();
        $settings->save();

        return response()->json([
            'success' => true,
            'message' => 'Countdown started successfully.',
            'end_time' => $settings->countdown_end_time->toIso8601String()
        ]);
    }

    public function autoFormGroups(Request $request)
    {
        $settings = GroupSetting::first();
        if (!$settings) {
            return response()->json(['success' => false, 'message' => 'Group settings not found.'], 404);
        }

        try {
            DB::beginTransaction();

            // 1. Get all students not in a group
            $studentsInGroups = GroupMember::where('status', 'joined')->pluck('user_id');
            $unassignedStudents = User::role('student')
                ->whereNotIn('id', $studentsInGroups)
                ->with(['studentSkillsSurvey'])
                ->get();

            if ($unassignedStudents->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'No unassigned students found.']);
            }

            // 2. Prepare data for balancing
            $participantsPerGroup = $settings->participants_per_group ?: 5;
            $numGroups = ceil($unassignedStudents->count() / $participantsPerGroup);
            
            // 3. Shuffle or sort for balancing
            if ($settings->balance_by_skills) {
                // Simplified skill sorting: count surveyed skills
                $unassignedStudents = $unassignedStudents->sortByDesc(function($student) {
                    if (!$student->studentSkillsSurvey) return 0;
                    $responses = json_decode($student->studentSkillsSurvey->responses, true) ?: [];
                    return count($responses, COUNT_RECURSIVE);
                });
            } else {
                $unassignedStudents = $unassignedStudents->shuffle();
            }

            // 4. Distribute into groups
            $groups = [];
            $lastGroupNumber = Group::where('name', 'LIKE', 'Group No %')->count();
            
            for ($i = 0; $i < $numGroups; $i++) {
                $project = Project::inRandomOrder()->first();
                $supervisor = User::role('supervisor')->inRandomOrder()->first();
                $groupNumber = $lastGroupNumber + $i + 1;
                
                $group = Group::create([
                    'name' => 'Group No ' . $groupNumber,
                    'project_id' => $project ? $project->id : null,
                    'supervisor_id' => $supervisor ? $supervisor->id : null,
                    'max_members' => $participantsPerGroup,
                    'status' => 'active',
                    'created_by' => Auth::id(),
                    'formed_at' => now(),
                ]);
                $groups[] = $group;
            }

            $groupIndex = 0;
            foreach ($unassignedStudents as $student) {
                GroupMember::create([
                    'group_id' => $groups[$groupIndex % $numGroups]->id,
                    'user_id' => $student->id,
                    'role' => ($groupIndex < $numGroups) ? 'leader' : 'member',
                    'status' => 'joined',
                    'joined_at' => now(),
                ]);
                $groupIndex++;
            }

            // Deactivate countdown
            $settings->is_active = false;
            $settings->countdown_end_time = null;
            $settings->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Successfully formed ' . count($groups) . ' groups.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'participants_per_group' => 'required|integer|min:1',
            'countdown_minutes' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'auto_create_groups' => 'boolean',
            'balance_by_gender' => 'boolean',
            'balance_by_skills' => 'boolean',
        ]);

        $settings = GroupSetting::first();
        
        if (!$settings) {
            $settings = new GroupSetting();
            $settings->created_by = Auth::id();
        }

        $settings->fill($validated);
        
        // Handle countdown end time
        if ($request->has('is_active') && $request->is_active) {
            if (!$settings->is_active || $settings->countdown_minutes != $validated['countdown_minutes']) {
                $settings->countdown_end_time = now()->addMinutes($validated['countdown_minutes']);
            }
        } else {
            $settings->countdown_end_time = null;
        }

        $settings->is_active = $request->has('is_active');
        $settings->auto_create_groups = $request->has('auto_create_groups');
        $settings->balance_by_gender = $request->has('balance_by_gender');
        $settings->balance_by_skills = $request->has('balance_by_skills');
        $settings->updated_by = Auth::id();
        
        $settings->save();

        return redirect()->back()->with('success', 'Group settings updated successfully.');
    }
}

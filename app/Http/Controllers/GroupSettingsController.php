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

            // 2. Prepare groups
            $participantsPerGroup = $settings->participants_per_group ?: 5;
            $numGroups = ceil($unassignedStudents->count() / $participantsPerGroup);
            
            // Initialize balanced pools
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
                    'formation_criteria' => [
                        'strategy' => 'balanced_auto_formation',
                        'gender_balanced' => $settings->balance_by_gender,
                        'skills_balanced' => $settings->balance_by_skills
                    ]
                ]);
                $groups[] = $group;
            }

            // 3. Balancing logic
            $studentsToAssign = $unassignedStudents;

            // Strategy: Snake distribution for skills
            if ($settings->balance_by_skills) {
                // Score students by skills (more skills = higher score)
                $studentsToAssign = $studentsToAssign->map(function($student) {
                    $score = 0;
                    if ($student->studentSkillsSurvey) {
                        // Count skills
                        $score += count($student->studentSkillsSurvey->skills ?: []);
                        // Add weight for experience level
                        $levels = ['beginner' => 1, 'intermediate' => 3, 'advanced' => 5];
                        $score += $levels[strtolower($student->studentSkillsSurvey->experience_level)] ?? 0;
                    }
                    $student->temp_skill_score = $score;
                    return $student;
                })->sortByDesc('temp_skill_score');
            }

            // If gender balancing is enabled, we split by gender first, then distribute
            if ($settings->balance_by_gender) {
                $males = $studentsToAssign->where('gender', 'male')->values();
                $females = $studentsToAssign->where('gender', 'female')->values();
                $others = $studentsToAssign->whereNotIn('gender', ['male', 'female'])->values();

                $this->distributeBalanced($males, $groups, $numGroups);
                $this->distributeBalanced($females, $groups, $numGroups);
                $this->distributeBalanced($others, $groups, $numGroups);
            } else {
                $this->distributeBalanced($studentsToAssign->values(), $groups, $numGroups);
            }

            // Deactivate countdown
            $settings->is_active = false;
            $settings->countdown_end_time = null;
            $settings->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Successfully formed ' . count($groups) . ' groups with balancing applied.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Helper to distribute a list of students into groups in a balanced way
     */
    protected function distributeBalanced($students, $groups, $numGroups)
    {
        // Use a persistent counter to track next available group to maintain balance across calls
        static $currentGroupIndex = 0;

        foreach ($students as $student) {
            // Find a group that isn't full yet, starting from current index
            $assigned = false;
            for ($attempt = 0; $attempt < $numGroups; $attempt++) {
                $idx = ($currentGroupIndex + $attempt) % $numGroups;
                $targetGroup = $groups[$idx];
                
                if ($targetGroup->members()->count() < $targetGroup->max_members) {
                    GroupMember::create([
                        'group_id' => $targetGroup->id,
                        'user_id' => $student->id,
                        'role' => ($targetGroup->members()->count() === 0) ? 'leader' : 'member',
                        'status' => 'joined',
                        'joined_at' => now(),
                    ]);
                    
                    // Increment starting index for next student to ensure round-robin
                    $currentGroupIndex = ($idx + 1) % $numGroups;
                    $assigned = true;
                    break;
                }
            }

            // Fallback: if all groups are technically "full" but we still have students (rounding issues)
            if (!$assigned) {
                $idx = $currentGroupIndex % $numGroups;
                GroupMember::create([
                    'group_id' => $groups[$idx]->id,
                    'user_id' => $student->id,
                    'role' => 'member',
                    'status' => 'joined',
                    'joined_at' => now(),
                ]);
                $currentGroupIndex++;
            }
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

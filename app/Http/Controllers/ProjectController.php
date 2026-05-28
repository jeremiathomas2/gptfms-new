<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\ProjectPhase;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('student')) {
            $membership = $user->activeGroup()->with('group.project', 'group.supervisor', 'group.activeMembers.user')->first();
            $group = $membership?->group;
            $project = $group?->project;
            $isLeader = $membership && $membership->role === 'leader';
            if (!$project && $group) {
                $project = Project::query()->where('group_id', $group->id)->first();
                if ($project && !$group->project_id) {
                    $group->update(['project_id' => $project->id]);
                }
            }

            if (!$project && $group && $group->supervisor_id) {
                DB::transaction(function () use (&$project, $group) {
                    $lockedGroup = Group::query()->lockForUpdate()->find($group->id);
                    if (!$lockedGroup) {
                        return;
                    }

                    $existing = null;
                    if ($lockedGroup->project_id) {
                        $existing = Project::query()->find($lockedGroup->project_id);
                    }
                    if (!$existing) {
                        $existing = Project::query()->where('group_id', $lockedGroup->id)->latest()->first();
                    }

                    if (!$existing) {
                        $existing = Project::create([
                            'title' => 'Not approved Yet',
                            'description' => 'Pending supervisor approval.',
                            'supervisor_id' => $lockedGroup->supervisor_id,
                            'group_id' => $lockedGroup->id,
                            'status' => 'draft',
                            'priority' => 'medium',
                            'progress_percentage' => 0,
                        ]);
                    } else {
                        if (!$existing->group_id) {
                            $existing->update(['group_id' => $lockedGroup->id]);
                        }
                    }

                    if (!$lockedGroup->project_id) {
                        $lockedGroup->update([
                            'project_id' => $existing->id,
                            'status' => $lockedGroup->status === 'forming' ? 'active' : $lockedGroup->status,
                        ]);
                    }

                    $project = $existing;
                    $this->ensureProjectPhases($project);
                });
            }

            if ($project) {
                $project->load(['supervisor', 'group', 'phases', 'tasks.assignee']);
            }

            $tasks = $project
                ? Task::query()
                    ->where('project_id', $project->id)
                    ->where('assigned_to', $user->id)
                    ->latest()
                    ->get()
                : collect();

            $phaseMap = Project::PHASES;
            $phases = $project ? $this->ensureProjectPhases($project) : collect();

            return view('projects.show', [
                'mode' => 'student',
                'group' => $group,
                'project' => $project,
                'phases' => $phases,
                'phaseMap' => $phaseMap,
                'tasks' => $tasks,
                'isLeader' => $isLeader,
            ]);
        }

        if ($user->hasRole('supervisor')) {
            $projects = Project::query()
                ->where('supervisor_id', $user->id)
                ->with(['group', 'supervisor'])
                ->latest()
                ->paginate(10);
        } else {
            $projects = Project::with(['group', 'supervisor'])->latest()->paginate(10);
        }

        $supervisors = $user->hasRole('admin')
            ? User::role('supervisor')->orderBy('name')->get()
            : collect();

        $availableGroups = $user->hasRole('admin')
            ? Group::query()
                ->whereNull('project_id')
                ->orderBy('name')
                ->get()
            : collect();

        return view('projects.index', compact('projects', 'supervisors', 'availableGroups'));
    }

    public function store(Request $request)
    {
        abort_unless(Auth::user() && Auth::user()->hasRole('admin'), 403);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'course_code' => 'required|string|max:20',
            'description' => 'required|string',
            'end_date' => 'required|date',
            'supervisor_id' => 'required|exists:users,id',
            'group_id' => 'required|exists:groups,id',
        ]);

        $group = Group::find($validated['group_id']);
        if (!$group) {
            return response()->json(['success' => false, 'message' => 'Group not found'], 422);
        }

        if ($group->project_id) {
            return response()->json(['success' => false, 'message' => 'This group already has a project assigned.'], 422);
        }

        $project = null;
        DB::transaction(function () use (&$project, $validated, $group) {
            $project = Project::create([
                'title' => $validated['title'],
                'course_code' => $validated['course_code'],
                'description' => $validated['description'],
                'end_date' => $validated['end_date'],
                'supervisor_id' => $validated['supervisor_id'],
                'group_id' => $group->id,
                'status' => 'in_progress',
                'progress_percentage' => 0,
                'start_date' => now(),
            ]);

            $group->update([
                'project_id' => $project->id,
                'supervisor_id' => $validated['supervisor_id'],
                'status' => $group->status === 'forming' ? 'active' : $group->status,
            ]);

            $this->ensureProjectPhases($project);
        });

        return response()->json([
            'success' => true,
            'message' => 'Project created successfully!',
            'project' => $project
        ]);
    }

    public function show(Project $project)
    {
        $user = Auth::user();
        $isLeader = false;

        if ($user->hasRole('student')) {
            $membership = $user->activeGroup()->with('group')->first();
            if (!$membership || (int) $membership->group_id !== (int) $project->group_id) {
                abort(403);
            }
            $isLeader = $membership->role === 'leader';
        }

        if ($user->hasRole('supervisor') && (int) $project->supervisor_id !== (int) $user->id) {
            abort(403);
        }

        $project->load(['group.activeMembers.user', 'supervisor', 'phases.submittedBy', 'phases.reviewedBy', 'tasks.assignee', 'tasks.creator']);

        $phases = $this->ensureProjectPhases($project);
        $phaseMap = Project::PHASES;

        $mode = $user->hasRole('admin') ? 'admin' : ($user->hasRole('supervisor') ? 'supervisor' : 'student');

        $tasks = Task::query()
            ->where('project_id', $project->id)
            ->when($mode === 'student', fn ($q) => $q->where('assigned_to', $user->id))
            ->latest()
            ->get();

        $members = $project->group
            ? $project->group->activeMembers->pluck('user')->filter()->values()
            : collect();

        return view('projects.show', compact('mode', 'project', 'phases', 'phaseMap', 'tasks', 'members', 'isLeader'));
    }

    public function submitPhase(Request $request, Project $project, int $phaseNumber)
    {
        $user = Auth::user();
        abort_unless($user && $user->hasRole('student'), 403);

        $membership = $user->activeGroup()->first();
        if (!$membership || (int) $membership->group_id !== (int) $project->group_id) {
            abort(403);
        }
        if ($membership->role !== 'leader') {
            return response()->json(['message' => 'Only the group leader can submit phases.'], 403);
        }

        if (!array_key_exists($phaseNumber, Project::PHASES)) {
            return response()->json(['message' => 'Invalid phase.'], 422);
        }

        $validated = $request->validate([
            'submission' => 'required|string|max:20000',
        ]);

        $phases = $this->ensureProjectPhases($project)->keyBy('phase_number');
        for ($i = 1; $i < $phaseNumber; $i++) {
            if (!isset($phases[$i]) || $phases[$i]->status !== 'approved') {
                return response()->json(['message' => 'This phase is locked until previous phases are approved.'], 422);
            }
        }

        $phase = $phases[$phaseNumber];
        if ($phase->status === 'approved') {
            return response()->json(['message' => 'This phase is already approved.'], 422);
        }

        $phase->update([
            'submission' => $validated['submission'],
            'submitted_by' => $user->id,
            'submitted_at' => now(),
            'status' => 'submitted',
        ]);

        return response()->json(['message' => 'Phase submitted for supervisor approval.']);
    }

    public function reviewPhase(Request $request, Project $project, int $phaseNumber)
    {
        $user = Auth::user();
        abort_unless($user && ($user->hasRole('supervisor') || $user->hasRole('admin')), 403);

        if ($user->hasRole('supervisor') && (int) $project->supervisor_id !== (int) $user->id) {
            abort(403);
        }

        if (!array_key_exists($phaseNumber, Project::PHASES)) {
            return response()->json(['message' => 'Invalid phase.'], 422);
        }

        $validated = $request->validate([
            'action' => 'required|in:approve,changes',
            'supervisor_notes' => 'nullable|string|max:20000',
        ]);

        $phases = $this->ensureProjectPhases($project)->keyBy('phase_number');
        $phase = $phases[$phaseNumber];

        if ($phase->status === 'not_started') {
            return response()->json(['message' => 'No submission received for this phase.'], 422);
        }

        if ($validated['action'] === 'approve') {
            $phase->update([
                'status' => 'approved',
                'supervisor_notes' => $validated['supervisor_notes'],
                'reviewed_by' => $user->id,
                'reviewed_at' => now(),
            ]);

            if ($phaseNumber === 1 && $phase->submission) {
                $project->update(['title' => trim($phase->submission)]);
            }

            $progress = $project->getPhaseProgressPercent();
            $update = ['progress_percentage' => $progress];

            if ($phaseNumber === 6) {
                $update['status'] = 'completed';
                $update['end_date'] = $project->end_date ?: now();
                if ($project->group) {
                    $project->group->update(['status' => 'completed']);
                }
            } else {
                if ($project->status === 'draft') {
                    $update['status'] = 'in_progress';
                }
            }

            $project->update($update);

            return response()->json(['message' => 'Phase approved successfully.']);
        }

        $phase->update([
            'status' => 'changes_requested',
            'supervisor_notes' => $validated['supervisor_notes'],
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
        ]);

        return response()->json(['message' => 'Changes requested for this phase.']);
    }

    public function createPhaseTask(Request $request, Project $project, int $phaseNumber)
    {
        $user = Auth::user();
        abort_unless($user && ($user->hasRole('supervisor') || $user->hasRole('admin')), 403);

        if ($user->hasRole('supervisor') && (int) $project->supervisor_id !== (int) $user->id) {
            abort(403);
        }

        if (!array_key_exists($phaseNumber, Project::PHASES)) {
            return response()->json(['message' => 'Invalid phase.'], 422);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'assigned_to' => 'required|exists:users,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'due_date' => 'required|date',
        ]);

        $memberOk = GroupMember::query()
            ->where('group_id', $project->group_id)
            ->where('user_id', $validated['assigned_to'])
            ->where('status', 'joined')
            ->exists();

        if (!$memberOk) {
            return response()->json(['message' => 'Assignee must be an active member of the project group.'], 422);
        }

        Task::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'project_id' => $project->id,
            'assigned_to' => $validated['assigned_to'],
            'created_by' => $user->id,
            'status' => 'todo',
            'priority' => $validated['priority'],
            'due_date' => $validated['due_date'],
        ]);

        return response()->json(['message' => 'Task created and assigned successfully.']);
    }

    private function ensureProjectPhases(Project $project)
    {
        $existing = ProjectPhase::query()->where('project_id', $project->id)->get()->keyBy('phase_number');
        $created = [];

        foreach (Project::PHASES as $n => $title) {
            if (isset($existing[$n])) {
                $created[] = $existing[$n];
                continue;
            }

            $created[] = ProjectPhase::create([
                'project_id' => $project->id,
                'phase_number' => $n,
                'phase_title' => $title,
                'status' => 'not_started',
            ]);
        }

        return collect($created)->sortBy('phase_number')->values();
    }
}

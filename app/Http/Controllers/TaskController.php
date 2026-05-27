<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Project;
use App\Models\GroupMember;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $base = Task::query()->with(['assignee', 'project.group', 'creator']);

        if ($user->hasRole('student')) {
            $base->where('assigned_to', $user->id);
        } elseif ($user->hasRole('supervisor')) {
            $projectIds = Project::query()->where('supervisor_id', $user->id)->pluck('id');
            $base->whereIn('project_id', $projectIds);
        }

        $todoTasks = (clone $base)->where('status', 'todo')->latest()->get();
        $inProgressTasks = (clone $base)->whereIn('status', ['in_progress', 'review'])->latest()->get();
        $completedTasks = (clone $base)->where('status', 'completed')->latest()->get();

        $canCreate = $user->hasRole('admin') || $user->hasRole('supervisor');

        $projects = collect();
        $assignees = collect();

        if ($canCreate) {
            $projects = $user->hasRole('supervisor')
                ? Project::query()->where('supervisor_id', $user->id)->orderBy('title')->get()
                : Project::query()->orderBy('title')->get();

            if ($user->hasRole('supervisor')) {
                $groupIds = $projects->pluck('group_id')->filter()->unique()->values();
                $userIds = GroupMember::query()
                    ->whereIn('group_id', $groupIds)
                    ->where('status', 'joined')
                    ->pluck('user_id')
                    ->unique()
                    ->values();

                $assignees = User::query()->whereIn('id', $userIds)->orderBy('name')->get();
            } else {
                $assignees = User::query()->orderBy('name')->get();
            }
        }

        return view('tasks.index', compact('todoTasks', 'inProgressTasks', 'completedTasks', 'canCreate', 'projects', 'assignees'));
    }

    public function attentionCount()
    {
        $user = Auth::user();

        $attentionStatuses = ['todo', 'in_progress', 'review'];

        $assignedCount = Task::query()
            ->where('assigned_to', $user->id)
            ->whereIn('status', $attentionStatuses)
            ->count();

        $createdCount = Task::query()
            ->where('created_by', $user->id)
            ->whereIn('status', $attentionStatuses)
            ->count();

        $count = $assignedCount > 0 ? $assignedCount : $createdCount;

        return response()->json([
            'count' => $count,
            'assigned' => $assignedCount,
            'created' => $createdCount,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        abort_unless($user && ($user->hasRole('admin') || $user->hasRole('supervisor')), 403);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
            'assigned_to' => 'nullable|exists:users,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'due_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $project = Project::find($validated['project_id']);
        if (!$project) {
            return response()->json(['success' => false, 'message' => 'Project not found'], 422);
        }

        if ($user->hasRole('supervisor') && (int) $project->supervisor_id !== (int) $user->id) {
            return response()->json(['success' => false, 'message' => 'You can only create tasks for your own projects.'], 403);
        }

        if (!empty($validated['assigned_to'])) {
            $memberOk = GroupMember::query()
                ->where('group_id', $project->group_id)
                ->where('user_id', $validated['assigned_to'])
                ->where('status', 'joined')
                ->exists();

            if (!$memberOk) {
                return response()->json(['success' => false, 'message' => 'Assignee must be an active member of the project group.'], 422);
            }
        }

        $task = Task::create([
            'title' => $validated['title'],
            'project_id' => $validated['project_id'],
            'assigned_to' => $validated['assigned_to'],
            'priority' => $validated['priority'],
            'due_date' => $validated['due_date'],
            'description' => $validated['description'],
            'created_by' => $user->id,
            'status' => 'todo',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Task created successfully!',
            'task' => $task
        ]);
    }

    public function accept(Task $task)
    {
        $user = Auth::user();
        abort_unless($user && $user->hasRole('student'), 403);

        if ((int) $task->assigned_to !== (int) $user->id) {
            abort(403);
        }

        if ($task->status !== 'todo') {
            return response()->json(['message' => 'Only "To do" tasks can be accepted.'], 422);
        }

        $task->update([
            'status' => 'in_progress',
        ]);

        return response()->json(['message' => 'Task accepted and moved to In Progress.']);
    }

    public function complete(Task $task)
    {
        $user = Auth::user();
        abort_unless($user && $user->hasRole('student'), 403);

        if ((int) $task->assigned_to !== (int) $user->id) {
            abort(403);
        }

        if ($task->status === 'completed') {
            return response()->json(['message' => 'Task is already completed.'], 422);
        }

        $task->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return response()->json(['message' => 'Task marked as completed.']);
    }
}

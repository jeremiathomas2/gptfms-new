<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index()
    {
        $todoTasks = Task::with('assignee')->where('status', 'todo')->get();
        $inProgressTasks = Task::with('assignee')->where('status', 'in_progress')->get();
        $completedTasks = Task::with('assignee')->where('status', 'completed')->get();
        
        return view('tasks.index', compact('todoTasks', 'inProgressTasks', 'completedTasks'));
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
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
            'assigned_to' => 'nullable|exists:users,id',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $task = Task::create([
            'title' => $validated['title'],
            'project_id' => $validated['project_id'],
            'assigned_to' => $validated['assigned_to'],
            'priority' => $validated['priority'],
            'due_date' => $validated['due_date'],
            'description' => $validated['description'],
            'created_by' => Auth::id(),
            'status' => 'todo',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Task created successfully!',
            'task' => $task
        ]);
    }
}

@extends('layouts.app')

@section('breadcrumb', 'Tasks')

@section('content')
<div class="page active" id="page-tasks">
    <div class="section-header">
        <div><div class="section-title">Task Board</div><div class="section-sub">Manage your workflow across teams</div></div>
        <div style="display:flex;gap:8px">
            <select class="form-control" style="max-width:160px;padding:7px 12px;font-size:12.5px"><option>All Groups</option></select>
            <button class="btn btn-primary btn-sm" onclick="openModal('modal-task')"><i class="uil uil-plus me-1"></i> Add Task</button>
        </div>
    </div>
    <div class="kanban" style="grid-template-columns:repeat(3,1fr)">
        <div class="kanban-col">
            <div class="kanban-col-header"><span class="kanban-col-title"><i class="uil uil-list-ul me-2"></i> To Do</span><span class="kanban-count">{{ $todoTasks->count() }}</span></div>
            @foreach($todoTasks as $task)
            <div class="task-card">
                <div class="task-title">{{ $task->title }}</div>
                <div style="margin-bottom:6px">
                    <span class="badge {{ $task->priority === 'high' ? 'badge-red' : ($task->priority === 'medium' ? 'badge-amber' : 'badge-blue') }}">
                        {{ ucfirst($task->priority) }} Priority
                    </span>
                </div>
                <div class="task-meta">
                    <div class="avatar-group">
                        @if($task->assignee)
                        <div class="av" title="{{ $task->assignee->name }}">{{ $task->assignee->initials }}</div>
                        @else
                        <div class="av" title="Unassigned"><i class="uil uil-user-exclamation"></i></div>
                        @endif
                    </div>
                    <div class="task-due {{ $task->isOverdue() ? 'color-danger' : '' }}">
                        <i class="uil uil-calendar-alt me-1"></i> {{ $task->due_date ? $task->due_date->format('M d') : 'No date' }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="kanban-col">
            <div class="kanban-col-header"><span class="kanban-col-title"><i class="uil uil-spinner me-2"></i> In Progress</span><span class="kanban-count">{{ $inProgressTasks->count() }}</span></div>
            @foreach($inProgressTasks as $task)
            <div class="task-card" style="border-left:3px solid var(--primary)">
                <div class="task-title">{{ $task->title }}</div>
                <div style="margin-bottom:6px">
                    <span class="badge badge-blue">In Progress</span>
                </div>
                <div class="progress-wrap" style="margin-bottom:8px">
                    <div class="progress-bar"><div class="progress-fill" style="width:{{ $task->progress ?? 50 }}%"></div></div>
                </div>
                <div class="task-meta">
                    <div class="avatar-group">
                        @if($task->assignee)
                        <div class="av" title="{{ $task->assignee->name }}">{{ $task->assignee->initials }}</div>
                        @endif
                    </div>
                    <div class="task-due">
                        <i class="uil uil-calendar-alt me-1"></i> {{ $task->due_date ? $task->due_date->format('M d') : '' }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="kanban-col">
            <div class="kanban-col-header"><span class="kanban-col-title"><i class="uil uil-check-circle me-2"></i> Completed</span><span class="kanban-count">{{ $completedTasks->count() }}</span></div>
            @foreach($completedTasks as $task)
            <div class="task-card" style="opacity:.6">
                <div class="task-title">{{ $task->title }}</div>
                <div class="task-meta">
                    <span class="badge badge-green">Done</span>
                    <div class="task-due">
                        <i class="uil uil-calendar-alt me-1"></i> {{ $task->completed_at ? $task->completed_at->format('M d') : '' }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- ═══════════════ ADD TASK MODAL ═══════════════ -->
<div class="modal-overlay" id="modal-task">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">Add New Task</span>
            <span class="modal-close" onclick="closeModal('modal-task')"><i class="uil uil-multiply"></i></span>
        </div>
        <form id="addTaskForm">
            @csrf
            <div class="form-group">
                <label class="form-label">Task Title</label>
                <input name="title" class="form-control" placeholder="e.g. Implement authentication" required />
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Project</label>
                    <select name="project_id" class="form-control" required>
                        @php $projects = \App\Models\Project::all(); @endphp
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Priority</label>
                    <select name="priority" class="form-control">
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Assign To</label>
                    <select name="assigned_to" class="form-control">
                        <option value="">Unassigned</option>
                        @php $users = \App\Models\User::all(); @endphp
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Due Date</label>
                    <input name="due_date" type="date" class="form-control" required />
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3" placeholder="Task details..."></textarea>
            </div>
            <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:15px;">
                <button type="button" class="btn btn-outline" onclick="closeModal('modal-task')">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Task</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('addTaskForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch("{{ route('tasks.store') }}", {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            toast(data.message, '<i class="uil uil-check"></i>');
            closeModal('modal-task');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            toast(data.message || 'Error creating task', '<i class="uil uil-exclamation-triangle"></i>');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toast('An error occurred. Please try again.', '<i class="uil uil-exclamation-triangle"></i>');
    });
});
</script>
@endsection

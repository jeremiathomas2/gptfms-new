@extends('layouts.app')

@section('breadcrumb', 'Projects')

@section('content')
<div class="page active" id="page-projects">
    <div class="section-header">
        <div><div class="section-title">Projects</div><div class="section-sub">Track progress and deadlines</div></div>
        <button class="btn btn-primary btn-sm" onclick="openModal('modal-project')"><i class="uil uil-folder-plus me-1"></i> Create Project</button>
    </div>
    <div class="card" style="padding:0;overflow:hidden">
        <div style="padding:14px 18px;border-bottom:1px solid var(--border);display:flex;gap:8px;align-items:center">
            <input class="form-control" style="max-width:240px;padding:7px 12px" placeholder="🔍 Search projects…" />
            <select class="form-control" style="max-width:160px;padding:7px 12px"><option>All Status</option><option>Active</option><option>Completed</option><option>At Risk</option></select>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Project Name</th><th>Group</th><th>Course</th><th>Deadline</th><th>Status</th><th>Progress</th><th>Actions</th></tr></thead>
                <tbody>
                    @foreach($projects as $project)
                    <tr>
                        <td><strong>{{ $project->title }}</strong></td>
                        <td>{{ $project->group->name ?? 'Unassigned' }}</td>
                        <td>{{ $project->course_code }}</td>
                        <td><i class="uil uil-calendar-alt me-1"></i> {{ $project->deadline ? $project->deadline->format('M d, Y') : 'N/A' }}</td>
                        <td>
                            <span class="badge {{ $project->status === 'completed' ? 'badge-green' : ($project->status === 'at_risk' ? 'badge-red' : 'badge-amber') }}">
                                {{ ucfirst($project->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="progress-bar" style="width:120px">
                                <div class="progress-fill" style="width:{{ $project->progress_percentage }}%"></div>
                            </div>
                        </td>
                        <td>
                            <button class="btn btn-ghost btn-sm" onclick="toast('Opening project…','<i class=\'uil uil-folder-open\'></i>')"><i class="uil uil-eye"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="pagination-container">
        {{ $projects->links() }}
    </div>
</div>

<!-- ═══════════════ CREATE PROJECT MODAL ═══════════════ -->
<div class="modal-overlay" id="modal-project">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">Create New Project</span>
            <span class="modal-close" onclick="closeModal('modal-project')"><i class="uil uil-multiply"></i></span>
        </div>
        <form id="createProjectForm">
            @csrf
            <div class="form-group">
                <label class="form-label">Project Title</label>
                <input name="title" class="form-control" placeholder="e.g. AI Research Platform" required />
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Course Code</label>
                    <input name="course_code" class="form-control" placeholder="e.g. CS401" required />
                </div>
                <div class="form-group">
                    <label class="form-label">Deadline</label>
                    <input name="deadline" type="date" class="form-control" required />
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Supervisor</label>
                <select name="supervisor_id" class="form-control">
                    <option value="">Select Supervisor</option>
                    @php $supervisors = \App\Models\User::role('supervisor')->get(); @endphp
                    @foreach($supervisors as $supervisor)
                        <option value="{{ $supervisor->id }}">{{ $supervisor->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="4" placeholder="Project objectives and scope..." required></textarea>
            </div>
            <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:15px;">
                <button type="button" class="btn btn-outline" onclick="closeModal('modal-project')">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Project</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('createProjectForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch("{{ route('projects.store') }}", {
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
            closeModal('modal-project');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            toast(data.message || 'Error creating project', '<i class="uil uil-exclamation-triangle"></i>');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toast('An error occurred. Please try again.', '<i class="uil uil-exclamation-triangle"></i>');
    });
});
</script>
@endsection

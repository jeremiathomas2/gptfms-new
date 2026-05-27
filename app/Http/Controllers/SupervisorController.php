<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\Project;
use App\Models\ProjectPhase;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class SupervisorController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        abort_unless($user && $user->hasRole('supervisor'), 403);

        $projects = Project::query()
            ->where('supervisor_id', $user->id)
            ->with(['group', 'phases'])
            ->latest()
            ->get();

        $projectIds = $projects->pluck('id')->values();

        $myGroups = Group::query()
            ->where('supervisor_id', $user->id)
            ->with(['project'])
            ->withCount(['activeMembers'])
            ->orderBy('name')
            ->get();

        $pendingPhaseCount = ProjectPhase::query()
            ->whereIn('project_id', $projectIds)
            ->where('status', 'submitted')
            ->count();

        $latestPhaseSubmissions = ProjectPhase::query()
            ->whereIn('project_id', $projectIds)
            ->whereIn('status', ['submitted', 'changes_requested'])
            ->with(['project.group', 'submittedBy'])
            ->latest('submitted_at')
            ->take(8)
            ->get();

        $overdueTaskCount = Task::query()
            ->whereIn('project_id', $projectIds)
            ->overdue()
            ->count();

        $reviewTaskCount = Task::query()
            ->whereIn('project_id', $projectIds)
            ->where('status', 'review')
            ->count();

        $completedTaskCount = Task::query()
            ->whereIn('project_id', $projectIds)
            ->where('status', 'completed')
            ->count();

        $avgProgress = $projects->count() > 0 ? round($projects->avg('progress_percentage')) : 0;

        return view('supervisor.index', compact(
            'myGroups',
            'projects',
            'pendingPhaseCount',
            'latestPhaseSubmissions',
            'overdueTaskCount',
            'reviewTaskCount',
            'completedTaskCount',
            'avgProgress',
        ));
    }
}

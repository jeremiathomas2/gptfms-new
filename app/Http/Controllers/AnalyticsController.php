<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Project;
use App\Models\ProjectPhase;
use App\Models\Skill;
use App\Models\StudentSkillsSurvey;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            $groupCount = Group::count();
            $projectCount = Project::count();
            $activeProjectCount = Project::active()->count();
            $completedTaskCount = Task::query()->where('status', 'completed')->count();
            $avgProgress = (int) round((float) Project::query()->avg('progress_percentage'));

            $phaseCounts = ProjectPhase::query()
                ->select('status', DB::raw('COUNT(*) as c'))
                ->groupBy('status')
                ->pluck('c', 'status');

            $skillCounts = $this->topSkillCounts(StudentSkillsSurvey::query()->whereNotNull('completed_at')->get());

            $projects = Project::query()
                ->with(['group', 'supervisor'])
                ->latest()
                ->take(8)
                ->get();

            return view('analytics.index', [
                'mode' => 'admin',
                'groupCount' => $groupCount,
                'projectCount' => $projectCount,
                'activeProjectCount' => $activeProjectCount,
                'completedTaskCount' => $completedTaskCount,
                'avgProgress' => $avgProgress,
                'phaseCounts' => $phaseCounts,
                'skillCounts' => $skillCounts,
                'projects' => $projects,
            ]);
        }

        if ($user->hasRole('supervisor')) {
            $projects = Project::query()
                ->where('supervisor_id', $user->id)
                ->with(['group', 'phases'])
                ->latest()
                ->get();

            $projectIds = $projects->pluck('id')->values();
            $groupIds = $projects->pluck('group_id')->filter()->unique()->values();

            $myGroupsCount = Group::query()->where('supervisor_id', $user->id)->count();
            $avgProgress = $projects->count() > 0 ? (int) round($projects->avg('progress_percentage')) : 0;

            $pendingPhaseCount = ProjectPhase::query()
                ->whereIn('project_id', $projectIds)
                ->where('status', 'submitted')
                ->count();

            $phaseCounts = ProjectPhase::query()
                ->whereIn('project_id', $projectIds)
                ->select('status', DB::raw('COUNT(*) as c'))
                ->groupBy('status')
                ->pluck('c', 'status');

            $taskCounts = Task::query()
                ->whereIn('project_id', $projectIds)
                ->select('status', DB::raw('COUNT(*) as c'))
                ->groupBy('status')
                ->pluck('c', 'status');

            $completedTaskCount = Task::query()
                ->whereIn('project_id', $projectIds)
                ->where('status', 'completed')
                ->count();

            $studentIds = GroupMember::query()
                ->whereIn('group_id', $groupIds)
                ->where('status', 'joined')
                ->pluck('user_id')
                ->unique()
                ->values();

            $surveys = StudentSkillsSurvey::query()
                ->whereIn('user_id', $studentIds)
                ->whereNotNull('completed_at')
                ->get();

            $skillCounts = $this->topSkillCounts($surveys);

            return view('analytics.index', [
                'mode' => 'supervisor',
                'myGroupsCount' => $myGroupsCount,
                'projectCount' => $projects->count(),
                'pendingPhaseCount' => $pendingPhaseCount,
                'completedTaskCount' => $completedTaskCount,
                'avgProgress' => $avgProgress,
                'skillCounts' => $skillCounts,
                'phaseCounts' => $phaseCounts,
                'taskCounts' => $taskCounts,
                'projects' => $projects->take(8),
            ]);
        }

        $membership = $user->activeGroup()->with('group.project.phases', 'group.project.tasks')->first();
        $project = $membership?->group?->project;

        $progress = $project ? (int) round((float) $project->progress_percentage) : 0;

        $taskAttention = Task::query()
            ->where('assigned_to', $user->id)
            ->whereIn('status', ['todo', 'in_progress', 'review'])
            ->count();

        $taskCompleted = Task::query()
            ->where('assigned_to', $user->id)
            ->where('status', 'completed')
            ->count();

        $taskCounts = Task::query()
            ->where('assigned_to', $user->id)
            ->select('status', DB::raw('COUNT(*) as c'))
            ->groupBy('status')
            ->pluck('c', 'status');

        $currentPhase = null;
        $approvedPhases = 0;
        $phaseCounts = collect();
        if ($project) {
            $approvedPhases = $project->phases->where('status', 'approved')->count();
            $currentPhase = min($approvedPhases + 1, count(Project::PHASES));
            $phaseCounts = $project->phases
                ->groupBy('status')
                ->map(fn ($items) => $items->count());
        }

        $survey = $user->studentSkillsSurvey;
        $skillCounts = collect();
        if ($survey && $survey->completed_at) {
            $skillCounts = $this->topSkillCounts(collect([$survey]), 10);
        }

        return view('analytics.index', [
            'mode' => 'student',
            'project' => $project,
            'progress' => $progress,
            'taskAttention' => $taskAttention,
            'taskCompleted' => $taskCompleted,
            'approvedPhases' => $approvedPhases,
            'currentPhase' => $currentPhase,
            'skillCounts' => $skillCounts,
            'phaseCounts' => $phaseCounts,
            'taskCounts' => $taskCounts,
        ]);
    }

    private function topSkillCounts($surveys, int $limit = 10)
    {
        $counts = [];
        $idBag = [];
        $raw = [];

        foreach ($surveys as $s) {
            $skills = $s->skills ?? [];
            if (is_string($skills)) {
                $decoded = json_decode($skills, true);
                $skills = is_array($decoded) ? $decoded : [];
            } elseif ($skills instanceof \Illuminate\Support\Collection) {
                $skills = $skills->all();
            } elseif ($skills instanceof \Traversable) {
                $skills = iterator_to_array($skills);
            } elseif (!is_array($skills)) {
                $skills = [];
            }

            foreach ($skills as $skill) {
                if (is_array($skill)) {
                    $name = trim((string) ($skill['name'] ?? ''));
                    if ($name !== '') {
                        $raw[] = $name;
                    }
                    continue;
                }

                $str = trim((string) $skill);
                if ($str === '') {
                    continue;
                }

                if (ctype_digit($str) && (int) $str > 0) {
                    $idBag[] = (int) $str;
                    $raw[] = (int) $str;
                } else {
                    $raw[] = $str;
                }
            }
        }

        $idBag = collect($idBag)->unique()->values();
        $idToName = $idBag->isNotEmpty()
            ? Skill::query()->whereIn('id', $idBag)->pluck('name', 'id')
            : collect();

        foreach ($raw as $item) {
            if (is_int($item)) {
                $name = trim((string) ($idToName[$item] ?? ''));
                if ($name === '') {
                    $name = 'Skill #' . (string) $item;
                }
                $counts[$name] = ($counts[$name] ?? 0) + 1;
                continue;
            }

            $name = trim((string) $item);
            if ($name === '') {
                continue;
            }
            $counts[$name] = ($counts[$name] ?? 0) + 1;
        }

        arsort($counts);
        $top = array_slice($counts, 0, $limit, true);

        return collect($top);
    }
}

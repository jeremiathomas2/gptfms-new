<?php

namespace App\Http\Controllers;

use App\Models\PeerEvaluation;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PeerEvaluationController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('student')) {
            $activeMembership = $user->activeGroup()->with('group.activeMembers.user', 'group.project', 'group.supervisor')->first();
            $group = $activeMembership?->group;

            if (!$group) {
                return view('student.evaluation', [
                    'mode' => 'student',
                    'group' => null,
                    'project' => null,
                    'peers' => collect(),
                    'existingByEvaluated' => collect(),
                    'completedCount' => 0,
                    'totalPeers' => 0,
                ]);
            }

            $project = $group->project;
            $peerUsers = $group->activeMembers
                ->map(fn ($m) => $m->user)
                ->filter()
                ->reject(fn ($u) => (int) $u->id === (int) $user->id)
                ->values();

            $existing = PeerEvaluation::query()
                ->where('evaluator_id', $user->id)
                ->where('project_id', $group->project_id)
                ->get();

            $existingByEvaluated = $existing->keyBy('evaluated_id');
            $completedCount = $existing->where('status', 'submitted')->count();

            return view('student.evaluation', [
                'mode' => 'student',
                'group' => $group,
                'project' => $project,
                'peers' => $peerUsers,
                'existingByEvaluated' => $existingByEvaluated,
                'completedCount' => $completedCount,
                'totalPeers' => $peerUsers->count(),
            ]);
        }

        $evaluationsQuery = PeerEvaluation::query()
            ->submitted()
            ->with(['evaluated', 'evaluator', 'project']);

        if ($user->hasRole('supervisor')) {
            $projectIds = Project::query()
                ->where('supervisor_id', $user->id)
                ->pluck('id');
            $evaluationsQuery->whereIn('project_id', $projectIds);
        }

        $evaluations = $evaluationsQuery->get();

        $summary = $evaluations
            ->groupBy(fn ($e) => $e->project_id . ':' . $e->evaluated_id)
            ->map(function ($rows) {
                $first = $rows->first();
                return [
                    'project' => $first?->project,
                    'evaluated' => $first?->evaluated,
                    'submissions' => $rows->count(),
                    'avg_overall' => round((float) $rows->avg('overall_score'), 2),
                ];
            })
            ->values()
            ->sortBy(fn ($r) => ($r['project']?->name ?? '') . ' ' . ($r['evaluated']?->name ?? ''))
            ->values();

        return view('student.evaluation', [
            'mode' => 'summary',
            'summary' => $summary,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        abort_unless($user->hasRole('student'), 403);

        $activeMembership = $user->activeGroup()->with('group.activeMembers.user', 'group.project')->first();
        $group = $activeMembership?->group;
        if (!$group || !$group->project_id) {
            return back()->withErrors(['evaluation' => 'You must be in an active group with a project to submit evaluations.']);
        }

        $validated = $request->validate([
            'action' => 'required|in:draft,submit',
            'evaluations' => 'required|array|min:1',
            'evaluations.*.evaluated_id' => 'required|integer',
            'evaluations.*.contribution_score' => 'required|integer|min:1|max:5',
            'evaluations.*.teamwork_score' => 'required|integer|min:1|max:5',
            'evaluations.*.communication_score' => 'required|integer|min:1|max:5',
            'evaluations.*.quality_score' => 'required|integer|min:1|max:5',
            'evaluations.*.timeliness_score' => 'required|integer|min:1|max:5',
            'evaluations.*.comments' => 'nullable|string|max:2000',
        ]);

        $peerIds = $group->activeMembers
            ->map(fn ($m) => $m->user_id)
            ->filter()
            ->reject(fn ($id) => (int) $id === (int) $user->id)
            ->values();

        $status = $validated['action'] === 'submit' ? 'submitted' : 'draft';

        DB::transaction(function () use ($validated, $user, $group, $peerIds, $status) {
            foreach ($validated['evaluations'] as $row) {
                $evaluatedId = (int) $row['evaluated_id'];
                if (!$peerIds->contains($evaluatedId)) {
                    continue;
                }

                PeerEvaluation::query()->updateOrCreate(
                    [
                        'evaluator_id' => $user->id,
                        'evaluated_id' => $evaluatedId,
                        'project_id' => $group->project_id,
                    ],
                    [
                        'contribution_score' => (int) $row['contribution_score'],
                        'teamwork_score' => (int) $row['teamwork_score'],
                        'communication_score' => (int) $row['communication_score'],
                        'quality_score' => (int) $row['quality_score'],
                        'timeliness_score' => (int) $row['timeliness_score'],
                        'comments' => $row['comments'] ?? null,
                        'status' => $status,
                        'submitted_at' => $status === 'submitted' ? now() : null,
                    ]
                );
            }
        });

        return redirect()
            ->route('evaluation')
            ->with('status', $status === 'submitted' ? 'Peer evaluations submitted.' : 'Draft saved.');
    }
}


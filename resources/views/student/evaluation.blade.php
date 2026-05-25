@extends('layouts.app')

@section('breadcrumb', 'Peer Evaluation')

@section('content')
<div class="page active" id="page-evaluation">
    <div class="section-header">
        <div>
            <div class="section-title">Peer Evaluation</div>
            <div class="section-sub">
                @if(($mode ?? 'student') === 'student')
                    Rate your teammates fairly based on their real contribution.
                @else
                    Review submitted peer evaluations.
                @endif
            </div>
        </div>
    </div>

    @if(session('status'))
        <div class="card" style="padding:12px 14px;margin-bottom:14px;border:1px solid rgba(16,185,129,.35);background:rgba(16,185,129,.10)">
            <div style="font-weight:800;margin-bottom:2px">Done</div>
            <div style="color:var(--text-muted)">{{ session('status') }}</div>
        </div>
    @endif
    @if($errors->any())
        <div class="card" style="padding:12px 14px;margin-bottom:14px;border:1px solid rgba(255,80,120,.35);background:rgba(255,80,120,.10)">
            <div style="font-weight:800;margin-bottom:2px">Action failed</div>
            <div style="color:var(--text-muted)">{{ $errors->first() }}</div>
        </div>
    @endif

    @if(($mode ?? 'student') === 'student')
        @if(!$group)
            <div class="card" style="padding:18px">
                <div style="font-weight:900;margin-bottom:6px">No active group</div>
                <div style="color:var(--text-muted)">Join or get assigned to a group to start peer evaluation.</div>
            </div>
        @else
            @php
                $total = (int) ($totalPeers ?? 0);
                $completed = (int) ($completedCount ?? 0);
                $pct = $total > 0 ? round(($completed / $total) * 100) : 0;
            @endphp
            <div class="card" style="padding:18px">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:16px;flex-wrap:wrap">
                    <div style="font-size:13.5px;font-weight:700">
                        {{ $group->name }} — {{ $project?->name ?? 'Peer Evaluation' }}
                        @if($group->supervisor)
                            <span style="color:var(--text-muted);font-weight:600">· Supervisor: {{ $group->supervisor->name }}</span>
                        @endif
                    </div>
                    <div class="progress-wrap" style="width:240px;margin:0;min-width:240px">
                        <div class="progress-label">
                            <span>Submitted</span>
                            <span>{{ $completed }}/{{ $total }}</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width:{{ $pct }}%"></div>
                        </div>
                    </div>
                </div>

                @if($peers->isEmpty())
                    <div style="color:var(--text-muted)">No teammates found to evaluate.</div>
                @else
                    <form method="POST" action="{{ route('evaluation.store') }}">
                        @csrf

                        @foreach($peers as $i => $peer)
                            @php
                                $existing = ($existingByEvaluated ?? collect())->get($peer->id);
                                $defaultScore = 3;
                                $contribution = (int) ($existing?->contribution_score ?? $defaultScore);
                                $teamwork = (int) ($existing?->teamwork_score ?? $defaultScore);
                                $communication = (int) ($existing?->communication_score ?? $defaultScore);
                                $quality = (int) ($existing?->quality_score ?? $defaultScore);
                                $timeliness = (int) ($existing?->timeliness_score ?? $defaultScore);
                                $comments = (string) ($existing?->comments ?? '');
                                $isSubmitted = ($existing?->status ?? '') === 'submitted';
                            @endphp

                            <div class="eval-member" style="align-items:flex-start">
                                <div class="eval-av">{{ $peer->initials }}</div>
                                <div class="eval-info" style="min-width:220px">
                                    <div class="eval-name">{{ $peer->name }}</div>
                                    <div class="eval-role">{{ $peer->getRoleNames()->first() ?? 'Member' }}</div>
                                </div>

                                <input type="hidden" name="evaluations[{{ $i }}][evaluated_id]" value="{{ $peer->id }}">

                                <div class="eval-sliders" style="flex:1;min-width:320px">
                                    <div class="slider-row"><label>Contribution</label><input type="range" min="1" max="5" value="{{ $contribution }}" name="evaluations[{{ $i }}][contribution_score]" oninput="this.nextElementSibling.textContent=this.value"/><span>{{ $contribution }}</span></div>
                                    <div class="slider-row"><label>Teamwork</label><input type="range" min="1" max="5" value="{{ $teamwork }}" name="evaluations[{{ $i }}][teamwork_score]" oninput="this.nextElementSibling.textContent=this.value"/><span>{{ $teamwork }}</span></div>
                                    <div class="slider-row"><label>Communication</label><input type="range" min="1" max="5" value="{{ $communication }}" name="evaluations[{{ $i }}][communication_score]" oninput="this.nextElementSibling.textContent=this.value"/><span>{{ $communication }}</span></div>
                                    <div class="slider-row"><label>Quality</label><input type="range" min="1" max="5" value="{{ $quality }}" name="evaluations[{{ $i }}][quality_score]" oninput="this.nextElementSibling.textContent=this.value"/><span>{{ $quality }}</span></div>
                                    <div class="slider-row"><label>Timeliness</label><input type="range" min="1" max="5" value="{{ $timeliness }}" name="evaluations[{{ $i }}][timeliness_score]" oninput="this.nextElementSibling.textContent=this.value"/><span>{{ $timeliness }}</span></div>
                                    <div style="margin-top:10px">
                                        <label style="display:block;font-size:12px;color:var(--text-muted);margin-bottom:6px">Comments</label>
                                        <textarea class="form-control" name="evaluations[{{ $i }}][comments]" rows="2" maxlength="2000" placeholder="Optional comments">{{ $comments }}</textarea>
                                    </div>
                                </div>

                                @if($isSubmitted)
                                    <span class="badge badge-green" style="margin-left:12px;margin-top:6px"><i class="uil uil-check-circle me-1"></i> Submitted</span>
                                @else
                                    <span class="badge badge-amber" style="margin-left:12px;margin-top:6px"><i class="uil uil-hourglass me-1"></i> Draft</span>
                                @endif
                            </div>
                        @endforeach

                        <div style="margin-top:18px;display:flex;justify-content:flex-end;gap:8px;flex-wrap:wrap">
                            <button class="btn btn-outline" type="submit" name="action" value="draft">Save Draft</button>
                            <button class="btn btn-primary" type="submit" name="action" value="submit"><i class="uil uil-check-circle me-1"></i> Submit Evaluation</button>
                        </div>
                    </form>
                @endif
            </div>
        @endif
    @else
        <div class="card" style="padding:18px">
            <div class="section-title" style="font-size:15px;margin-bottom:12px">Submitted Results</div>
            @php
                $rows = $summary ?? collect();
            @endphp
            @if($rows->isEmpty())
                <div style="color:var(--text-muted)">No submitted peer evaluations yet.</div>
            @else
                <div class="table-wrap">
                    <table>
                        <thead>
                        <tr>
                            <th>Project</th>
                            <th>Student</th>
                            <th>Submissions</th>
                            <th>Average Score</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($rows as $r)
                            <tr>
                                <td><strong>{{ $r['project']?->name ?? '—' }}</strong></td>
                                <td>{{ $r['evaluated']?->name ?? '—' }}</td>
                                <td>{{ $r['submissions'] }}</td>
                                <td><span class="badge badge-blue">{{ number_format($r['avg_overall'], 2) }}/5</span></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection

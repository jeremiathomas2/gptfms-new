@extends('layouts.app')

@section('breadcrumb', 'Analytics')

@push('styles')
<style>
    .charts-grid{display:grid;grid-template-columns:repeat(12,minmax(0,1fr));gap:14px;margin-bottom:18px}
    .chart-card{grid-column:span 4;background:var(--card);border:1px solid var(--border);border-radius:14px;padding:16px}
    .chart-card.wide{grid-column:span 8}
    @media (max-width: 1100px){.chart-card,.chart-card.wide{grid-column:1/-1}}
    .chart-title{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:10px}
    .chart-title .label{font-weight:700}
    .chart-shell{display:flex;gap:14px;align-items:center}
    .chart-svg{width:160px;max-width:160px;aspect-ratio:1/1;flex:0 0 auto}
    .chart-legend{display:grid;gap:8px;min-width:0;flex:1}
    .chart-legend .row{display:flex;align-items:center;justify-content:space-between;gap:10px}
    .chart-legend .left{display:flex;align-items:center;gap:8px;min-width:0}
    .chart-legend .name{white-space:nowrap;overflow:hidden;text-overflow:ellipsis;color:var(--text)}
    .chart-legend .meta{color:var(--text-muted);white-space:nowrap}
    .dot{width:10px;height:10px;border-radius:999px;flex:0 0 auto}
    .chart-empty{color:var(--text-muted);padding:14px;border:1px dashed var(--border);border-radius:12px}
    .bar-svg{width:100%;height:auto}
    .chart-palette{--c1:#4f8cff;--c2:#22c55e;--c3:#f59e0b;--c4:#ef4444;--c5:#a855f7;--c6:#14b8a6}
</style>
@endpush

@section('content')
@php
    $mode = $mode ?? 'student';
    $skillCounts = $skillCounts ?? collect();
    $projects = $projects ?? collect();
@endphp

<div class="page active" id="page-reports">
    <div class="section-header">
        <div>
            <div class="section-title">Analytics</div>
            <div class="section-sub">
                @if($mode === 'admin')
                    System-wide progress, skills distribution, and project health.
                @elseif($mode === 'supervisor')
                    Insights for your supervised projects and students.
                @else
                    Your project progress, tasks, and skills summary.
                @endif
            </div>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap">
            <a class="btn btn-outline btn-sm" href="{{ route('projects') }}"><i class="uil uil-folder me-1"></i> Projects</a>
            <a class="btn btn-outline btn-sm" href="{{ route('tasks') }}"><i class="uil uil-check-circle me-1"></i> Tasks</a>
        </div>
    </div>

    @if($mode === 'admin')
        <div class="grid-4" style="margin-bottom:18px">
            <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">Groups</div><div class="stat-value">{{ (int) ($groupCount ?? 0) }}</div></div><div class="stat-icon si-blue"><i class="uil uil-users-alt"></i></div></div></div>
            <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">Active Projects</div><div class="stat-value">{{ (int) ($activeProjectCount ?? 0) }}</div></div><div class="stat-icon si-green"><i class="uil uil-folder"></i></div></div></div>
            <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">Tasks Completed</div><div class="stat-value">{{ (int) ($completedTaskCount ?? 0) }}</div></div><div class="stat-icon si-amber"><i class="uil uil-check-circle"></i></div></div></div>
            <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">Avg. Progress</div><div class="stat-value">{{ (int) ($avgProgress ?? 0) }}%</div></div><div class="stat-icon si-red"><i class="uil uil-analytics"></i></div></div></div>
        </div>

        <div class="charts-grid chart-palette">
            <div class="chart-card">
                <div class="chart-title">
                    <div class="label"><i class="uil uil-chart-pie me-2"></i> Phase Status</div>
                    <div class="meta">{{ (int) (($phaseCounts ?? collect())->sum()) }}</div>
                </div>
                <div class="js-pie" data-data='@json(($phaseCounts ?? collect())->toArray())' data-kind="phases"></div>
            </div>
            <div class="chart-card wide">
                <div class="chart-title">
                    <div class="label"><i class="uil uil-chart-line me-2"></i> Project Progress</div>
                </div>
                @php
                    $series = collect($projects ?? collect())
                        ->reverse()
                        ->values()
                        ->map(fn ($p) => ['label' => (string) $p->title, 'value' => (int) round((float) $p->progress_percentage)]);
                @endphp
                <div class="js-line" data-series='@json($series)'></div>
            </div>
            <div class="chart-card">
                <div class="chart-title">
                    <div class="label"><i class="uil uil-lightbulb-alt me-2"></i> Top Skills</div>
                </div>
                <div class="js-bars" data-data='@json(($skillCounts ?? collect())->toArray())'></div>
            </div>
        </div>

        <div class="grid-7030">
            <div class="card" style="padding:18px">
                <div class="section-title" style="font-size:15px;margin-bottom:12px"><i class="uil uil-chart-line me-2"></i> Latest Projects</div>
                <div class="table-wrap">
                    <table>
                        <thead><tr><th>Project</th><th>Group</th><th>Supervisor</th><th>Status</th><th>Progress</th></tr></thead>
                        <tbody>
                        @foreach(($projects ?? collect()) as $p)
                            <tr>
                                <td><a href="{{ route('projects.show', $p) }}"><strong>{{ $p->title }}</strong></a></td>
                                <td>{{ $p->group?->name ?? '—' }}</td>
                                <td>{{ $p->supervisor?->name ?? '—' }}</td>
                                <td><span class="badge {{ $p->status === 'completed' ? 'badge-green' : 'badge-amber' }}">{{ str_replace('_',' ', ucfirst($p->status)) }}</span></td>
                                <td>
                                    <div class="progress-bar" style="width:140px">
                                        <div class="progress-fill" style="width:{{ (float) $p->progress_percentage }}%"></div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card" style="padding:18px">
                <div class="section-title" style="font-size:15px;margin-bottom:12px"><i class="uil uil-chart-pie me-2"></i> Phase Breakdown</div>
                <div class="js-pie" data-data='@json(($phaseCounts ?? collect())->toArray())' data-kind="phases"></div>
            </div>
        </div>

    @elseif($mode === 'supervisor')
        <div class="grid-4" style="margin-bottom:18px">
            <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">My Groups</div><div class="stat-value">{{ (int) ($myGroupsCount ?? 0) }}</div></div><div class="stat-icon si-blue"><i class="uil uil-users-alt"></i></div></div></div>
            <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">My Projects</div><div class="stat-value">{{ (int) ($projectCount ?? 0) }}</div></div><div class="stat-icon si-green"><i class="uil uil-folder"></i></div></div></div>
            <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">Pending Phase Reviews</div><div class="stat-value">{{ (int) ($pendingPhaseCount ?? 0) }}</div></div><div class="stat-icon si-amber"><i class="uil uil-clipboard-notes"></i></div></div></div>
            <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">Avg. Progress</div><div class="stat-value">{{ (int) ($avgProgress ?? 0) }}%</div></div><div class="stat-icon si-red"><i class="uil uil-analytics"></i></div></div></div>
        </div>

        <div class="charts-grid chart-palette">
            <div class="chart-card">
                <div class="chart-title">
                    <div class="label"><i class="uil uil-chart-pie me-2"></i> Phase Status</div>
                    <div class="meta">{{ (int) (($phaseCounts ?? collect())->sum()) }}</div>
                </div>
                <div class="js-pie" data-data='@json(($phaseCounts ?? collect())->toArray())' data-kind="phases"></div>
            </div>
            <div class="chart-card">
                <div class="chart-title">
                    <div class="label"><i class="uil uil-chart-pie me-2"></i> Task Status</div>
                    <div class="meta">{{ (int) (($taskCounts ?? collect())->sum()) }}</div>
                </div>
                <div class="js-pie" data-data='@json(($taskCounts ?? collect())->toArray())' data-kind="tasks"></div>
            </div>
            <div class="chart-card wide">
                <div class="chart-title">
                    <div class="label"><i class="uil uil-chart-line me-2"></i> Project Progress</div>
                </div>
                @php
                    $series = collect($projects ?? collect())
                        ->reverse()
                        ->values()
                        ->map(fn ($p) => ['label' => (string) $p->title, 'value' => (int) round((float) $p->progress_percentage)]);
                @endphp
                <div class="js-line" data-series='@json($series)'></div>
            </div>
            <div class="chart-card">
                <div class="chart-title">
                    <div class="label"><i class="uil uil-lightbulb-alt me-2"></i> Top Skills</div>
                </div>
                <div class="js-bars" data-data='@json(($skillCounts ?? collect())->toArray())'></div>
            </div>
        </div>

        <div class="grid-7030">
            <div class="card" style="padding:18px">
                <div class="section-title" style="font-size:15px;margin-bottom:12px"><i class="uil uil-folder me-2"></i> Project Progress</div>
                <div style="display:grid;gap:10px">
                    @forelse(($projects ?? collect()) as $p)
                        <div class="task-card" style="padding:12px">
                            <div class="task-title" style="display:flex;justify-content:space-between;gap:10px">
                                <a href="{{ route('projects.show', $p) }}"><strong>{{ $p->title }}</strong></a>
                                <span class="badge {{ $p->status === 'completed' ? 'badge-green' : 'badge-amber' }}">{{ str_replace('_',' ', ucfirst($p->status)) }}</span>
                            </div>
                            <div style="color:var(--text-muted);margin-top:4px">{{ $p->group?->name ?? '—' }}</div>
                            <div class="progress-bar" style="height:10px;margin-top:10px">
                                <div class="progress-fill" style="width:{{ (float) $p->progress_percentage }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div style="color:var(--text-muted)">No projects assigned yet.</div>
                    @endforelse
                </div>
            </div>
            <div class="card" style="padding:18px">
                <div class="section-title" style="font-size:15px;margin-bottom:12px"><i class="uil uil-chart-pie me-2"></i> Phase Breakdown</div>
                <div class="js-pie" data-data='@json(($phaseCounts ?? collect())->toArray())' data-kind="phases"></div>
            </div>
        </div>

    @else
        <div class="grid-4" style="margin-bottom:18px">
            <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">Project Progress</div><div class="stat-value">{{ (int) ($progress ?? 0) }}%</div></div><div class="stat-icon si-green"><i class="uil uil-folder"></i></div></div></div>
            <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">Approved Phases</div><div class="stat-value">{{ (int) ($approvedPhases ?? 0) }}/6</div></div><div class="stat-icon si-blue"><i class="uil uil-steps"></i></div></div></div>
            <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">Tasks Need Attention</div><div class="stat-value">{{ (int) ($taskAttention ?? 0) }}</div></div><div class="stat-icon si-amber"><i class="uil uil-clipboard-notes"></i></div></div></div>
            <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">Tasks Completed</div><div class="stat-value">{{ (int) ($taskCompleted ?? 0) }}</div></div><div class="stat-icon si-red"><i class="uil uil-check-circle"></i></div></div></div>
        </div>

        <div class="charts-grid chart-palette">
            <div class="chart-card">
                <div class="chart-title">
                    <div class="label"><i class="uil uil-chart-pie me-2"></i> Phase Status</div>
                    <div class="meta">{{ (int) (($phaseCounts ?? collect())->sum()) }}</div>
                </div>
                <div class="js-pie" data-data='@json(($phaseCounts ?? collect())->toArray())' data-kind="phases"></div>
            </div>
            <div class="chart-card">
                <div class="chart-title">
                    <div class="label"><i class="uil uil-chart-pie me-2"></i> Task Status</div>
                    <div class="meta">{{ (int) (($taskCounts ?? collect())->sum()) }}</div>
                </div>
                <div class="js-pie" data-data='@json(($taskCounts ?? collect())->toArray())' data-kind="tasks"></div>
            </div>
            <div class="chart-card wide">
                <div class="chart-title">
                    <div class="label"><i class="uil uil-chart-bar me-2"></i> My Skills</div>
                </div>
                <div class="js-bars" data-data='@json(($skillCounts ?? collect())->toArray())'></div>
            </div>
        </div>

        <div class="grid-7030">
            <div class="card" style="padding:18px">
                <div class="section-title" style="font-size:15px;margin-bottom:12px"><i class="uil uil-focus me-2"></i> Current Phase</div>
                @if(!empty($project))
                    <div class="task-card" style="padding:14px">
                        <div class="task-title" style="display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap">
                            <span><strong>{{ $project->title }}</strong></span>
                            <span class="badge badge-amber">Phase {{ (int) ($currentPhase ?? 1) }}</span>
                        </div>
                        <div style="color:var(--text-muted);margin-top:6px">Open “Project” menu to submit your phase work for approval.</div>
                        <div style="margin-top:12px">
                            <a class="btn btn-primary btn-sm" href="{{ route('projects') }}"><i class="uil uil-folder-open me-1"></i> Open Project</a>
                        </div>
                    </div>
                @else
                    <div style="color:var(--text-muted)">No project assigned yet.</div>
                @endif
            </div>
            <div class="card" style="padding:18px">
                <div class="section-title" style="font-size:15px;margin-bottom:12px"><i class="uil uil-chart-bar me-2"></i> My Skills</div>
                <div class="js-bars" data-data='@json(($skillCounts ?? collect())->toArray())'></div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const palette = ['var(--c1)', 'var(--c2)', 'var(--c3)', 'var(--c4)', 'var(--c5)', 'var(--c6)'];

        const titleize = (s) => String(s || '')
            .replace(/_/g, ' ')
            .replace(/\b\w/g, (m) => m.toUpperCase());

        const statusLabel = (kind, key) => {
            const k = String(key || '');
            if (kind === 'tasks') {
                const map = { todo: 'To do', in_progress: 'In progress', review: 'In review', completed: 'Completed' };
                return map[k] || titleize(k);
            }
            const map = { not_started: 'Not started', submitted: 'Submitted', approved: 'Approved', changes_requested: 'Changes requested' };
            return map[k] || titleize(k);
        };

        const parseMap = (raw) => {
            if (!raw || typeof raw !== 'object') return [];
            return Object.keys(raw).map((k) => ({ key: k, value: Number(raw[k] ?? 0) || 0 }));
        };

        const el = (tag, attrs = {}, children = []) => {
            const n = document.createElement(tag);
            Object.keys(attrs).forEach((k) => {
                if (k === 'class') n.className = attrs[k];
                else if (k === 'html') n.innerHTML = attrs[k];
                else n.setAttribute(k, attrs[k]);
            });
            children.forEach((c) => n.appendChild(c));
            return n;
        };

        const renderPie = (mount, kind, dataMap) => {
            const order = kind === 'tasks'
                ? { completed: 1, review: 2, in_progress: 3, todo: 4 }
                : { approved: 1, submitted: 2, changes_requested: 3, not_started: 4 };

            const data = parseMap(dataMap)
                .filter((d) => d.value > 0)
                .sort((a, b) => (order[a.key] ?? 99) - (order[b.key] ?? 99));
            const total = data.reduce((a, b) => a + b.value, 0);
            if (!total) {
                mount.innerHTML = '';
                mount.appendChild(el('div', { class: 'chart-empty' }, [document.createTextNode('No data available.')]));
                return;
            }

            const size = 160;
            const r = 56;
            const c = 2 * Math.PI * r;
            let offset = 0;

            const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
            svg.setAttribute('viewBox', `0 0 ${size} ${size}`);
            svg.setAttribute('class', 'chart-svg');
            svg.setAttribute('role', 'img');
            svg.setAttribute('aria-label', `${kind} distribution`);

            const bg = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
            bg.setAttribute('cx', String(size / 2));
            bg.setAttribute('cy', String(size / 2));
            bg.setAttribute('r', String(r));
            bg.setAttribute('fill', 'none');
            bg.setAttribute('stroke', 'var(--border)');
            bg.setAttribute('stroke-width', '18');
            svg.appendChild(bg);

            data.forEach((d, i) => {
                const dash = (d.value / total) * c;
                const seg = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                seg.setAttribute('cx', String(size / 2));
                seg.setAttribute('cy', String(size / 2));
                seg.setAttribute('r', String(r));
                seg.setAttribute('fill', 'none');
                seg.setAttribute('stroke', palette[i % palette.length]);
                seg.setAttribute('stroke-width', '18');
                seg.setAttribute('stroke-linecap', 'butt');
                seg.setAttribute('stroke-dasharray', `${dash} ${c - dash}`);
                seg.setAttribute('stroke-dashoffset', String(-offset));
                seg.setAttribute('transform', `rotate(-90 ${size / 2} ${size / 2})`);
                svg.appendChild(seg);
                offset += dash;
            });

            const primaryKey = kind === 'tasks' ? 'completed' : 'approved';
            const primaryLabel = kind === 'tasks' ? 'Done' : 'Approved';
            const primaryValue = data.find((d) => d.key === primaryKey)?.value ?? 0;
            const primaryPct = Math.round((primaryValue / total) * 100);

            const centerTop = document.createElementNS('http://www.w3.org/2000/svg', 'text');
            centerTop.setAttribute('x', String(size / 2));
            centerTop.setAttribute('y', String(size / 2 - 6));
            centerTop.setAttribute('text-anchor', 'middle');
            centerTop.setAttribute('dominant-baseline', 'middle');
            centerTop.setAttribute('fill', 'var(--text)');
            centerTop.setAttribute('font-size', '18');
            centerTop.setAttribute('font-weight', '800');
            centerTop.textContent = `${primaryPct}%`;
            svg.appendChild(centerTop);

            const centerBottom = document.createElementNS('http://www.w3.org/2000/svg', 'text');
            centerBottom.setAttribute('x', String(size / 2));
            centerBottom.setAttribute('y', String(size / 2 + 14));
            centerBottom.setAttribute('text-anchor', 'middle');
            centerBottom.setAttribute('dominant-baseline', 'middle');
            centerBottom.setAttribute('fill', 'var(--text-muted)');
            centerBottom.setAttribute('font-size', '12');
            centerBottom.textContent = primaryLabel;
            svg.appendChild(centerBottom);

            const legend = el('div', { class: 'chart-legend' });
            data.forEach((d, i) => {
                const pct = Math.round((d.value / total) * 100);
                legend.appendChild(el('div', { class: 'row' }, [
                    el('div', { class: 'left' }, [
                        el('span', { class: 'dot', style: `background:${palette[i % palette.length]}` }),
                        el('span', { class: 'name', title: statusLabel(kind, d.key) }, [document.createTextNode(statusLabel(kind, d.key))]),
                    ]),
                    el('span', { class: 'meta' }, [document.createTextNode(`${d.value} (${pct}%)`)]),
                ]));
            });

            mount.innerHTML = '';
            mount.appendChild(el('div', { class: 'chart-shell' }, [svg, legend]));
        };

        const renderBars = (mount, dataMap) => {
            const data = parseMap(dataMap).filter((d) => d.value > 0);
            if (!data.length) {
                mount.innerHTML = '';
                mount.appendChild(el('div', { class: 'chart-empty' }, [document.createTextNode('No data available.')]));
                return;
            }

            data.sort((a, b) => b.value - a.value);
            const max = Math.max(...data.map((d) => d.value), 1);
            const rowH = 22;
            const pad = 12;
            const h = pad * 2 + data.length * rowH;
            const w = 520;
            const labelW = 170;
            const barW = w - labelW - pad * 2 - 40;

            const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
            svg.setAttribute('viewBox', `0 0 ${w} ${h}`);
            svg.setAttribute('class', 'bar-svg');
            svg.setAttribute('role', 'img');
            svg.setAttribute('aria-label', 'Bar chart');

            data.forEach((d, i) => {
                const y = pad + i * rowH;
                const pct = d.value / max;
                const bar = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
                bar.setAttribute('x', String(labelW));
                bar.setAttribute('y', String(y + 3));
                bar.setAttribute('width', String(Math.round(barW * pct)));
                bar.setAttribute('height', '14');
                bar.setAttribute('rx', '7');
                bar.setAttribute('fill', palette[i % palette.length]);
                svg.appendChild(bar);

                const label = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                label.setAttribute('x', String(0));
                label.setAttribute('y', String(y + 14));
                label.setAttribute('fill', 'var(--text)');
                label.setAttribute('font-size', '12');
                label.textContent = String(d.key);
                svg.appendChild(label);

                const val = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                val.setAttribute('x', String(labelW + barW + 10));
                val.setAttribute('y', String(y + 14));
                val.setAttribute('fill', 'var(--text-muted)');
                val.setAttribute('font-size', '12');
                val.textContent = String(d.value);
                svg.appendChild(val);
            });

            mount.innerHTML = '';
            mount.appendChild(svg);
        };

        const renderLine = (mount, seriesRaw) => {
            const series = Array.isArray(seriesRaw) ? seriesRaw : [];
            if (!series.length) {
                mount.innerHTML = '';
                mount.appendChild(el('div', { class: 'chart-empty' }, [document.createTextNode('No data available.')]));
                return;
            }

            const w = 640;
            const h = 200;
            const padX = 18;
            const padY = 22;
            const innerW = w - padX * 2;
            const innerH = h - padY * 2 - 18;
            const n = series.length;

            const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
            svg.setAttribute('viewBox', `0 0 ${w} ${h}`);
            svg.setAttribute('class', 'bar-svg');
            svg.setAttribute('role', 'img');
            svg.setAttribute('aria-label', 'Line chart');

            const grid = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
            grid.setAttribute('x', String(padX));
            grid.setAttribute('y', String(padY));
            grid.setAttribute('width', String(innerW));
            grid.setAttribute('height', String(innerH));
            grid.setAttribute('fill', 'transparent');
            grid.setAttribute('stroke', 'var(--border)');
            grid.setAttribute('rx', '10');
            svg.appendChild(grid);

            const xStep = n > 1 ? innerW / (n - 1) : 0;
            const points = series.map((p, i) => {
                const v = Math.max(0, Math.min(100, Number(p.value ?? 0)));
                const x = padX + i * xStep;
                const y = padY + (1 - v / 100) * innerH;
                return { x, y, v, label: String(p.label ?? '') };
            });

            const poly = document.createElementNS('http://www.w3.org/2000/svg', 'polyline');
            poly.setAttribute('fill', 'none');
            poly.setAttribute('stroke', 'var(--c1)');
            poly.setAttribute('stroke-width', '3');
            poly.setAttribute('points', points.map((p) => `${p.x},${p.y}`).join(' '));
            svg.appendChild(poly);

            points.forEach((p, i) => {
                const dot = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                dot.setAttribute('cx', String(p.x));
                dot.setAttribute('cy', String(p.y));
                dot.setAttribute('r', '4');
                dot.setAttribute('fill', 'var(--c2)');
                svg.appendChild(dot);

                if (n <= 6 || i % 2 === 0) {
                    const t = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                    t.setAttribute('x', String(p.x));
                    t.setAttribute('y', String(padY + innerH + 16));
                    t.setAttribute('text-anchor', 'middle');
                    t.setAttribute('fill', 'var(--text-muted)');
                    t.setAttribute('font-size', '11');
                    t.textContent = p.label.length > 12 ? p.label.slice(0, 12) + '…' : p.label;
                    svg.appendChild(t);
                }
            });

            mount.innerHTML = '';
            mount.appendChild(svg);
        };

        document.querySelectorAll('.js-pie').forEach((m) => {
            let data = {};
            try { data = JSON.parse(m.getAttribute('data-data') || '{}'); } catch (e) { data = {}; }
            const kind = m.getAttribute('data-kind') || 'phases';
            renderPie(m, kind, data);
        });

        document.querySelectorAll('.js-bars').forEach((m) => {
            let data = {};
            try { data = JSON.parse(m.getAttribute('data-data') || '{}'); } catch (e) { data = {}; }
            renderBars(m, data);
        });

        document.querySelectorAll('.js-line').forEach((m) => {
            let series = [];
            try { series = JSON.parse(m.getAttribute('data-series') || '[]'); } catch (e) { series = []; }
            renderLine(m, series);
        });
    })();
</script>
@endpush

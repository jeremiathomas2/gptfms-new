@extends('layouts.app')

@section('breadcrumb', 'Analytics')

@section('content')
<div class="page active" id="page-reports">
    <div class="section-header">
        <div><div class="section-title">Analytics & Reports</div><div class="section-sub">Performance insights across all groups</div></div>
        <div style="display:flex;gap:8px">
            <select class="form-control" style="max-width:140px;padding:7px 12px;font-size:12.5px"><option>All Courses</option><option>CS401</option><option>CS302</option></select>
            <button class="btn btn-outline btn-sm" onclick="toast('Exporting PDF…','<i class=\'uil uil-file-download\'></i>')"><i class="uil uil-file-download me-1"></i> Export PDF</button>
        </div>
    </div>
    <div class="grid-4" style="margin-bottom:18px">
        <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">Total Groups</div><div class="stat-value">8</div></div><div class="stat-icon si-blue"><i class="uil uil-users-alt"></i></div></div></div>
        <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">Active Projects</div><div class="stat-value">12</div></div><div class="stat-icon si-green"><i class="uil uil-folder"></i></div></div></div>
        <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">Tasks Completed</div><div class="stat-value">147</div><div class="stat-change up"><i class="uil uil-arrow-up"></i> 23 this week</div></div><div class="stat-icon si-amber"><i class="uil uil-check-circle"></i></div></div></div>
        <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">Avg. Progress</div><div class="stat-value">63%</div></div><div class="stat-icon si-red"><i class="uil uil-analytics"></i></div></div></div>
    </div>
    <div class="grid-2">
        <div class="card">
            <div class="section-title" style="font-size:14px;margin-bottom:14px">Group Performance</div>
            <svg class="bar-chart-svg" viewBox="0 0 340 160" xmlns="http://www.w3.org/2000/svg">
                <defs><linearGradient id="barGrad1" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#2563EB"/><stop offset="100%" stop-color="#06B6D4"/></linearGradient>
                <linearGradient id="barGrad2" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#10B981"/><stop offset="100%" stop-color="#84CC16"/></linearGradient>
                <linearGradient id="barGrad3" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#F59E0B"/><stop offset="100%" stop-color="#EF4444"/></linearGradient>
                <linearGradient id="barGrad4" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#8B5CF6"/><stop offset="100%" stop-color="#2563EB"/></linearGradient></defs>
                <line x1="0" y1="130" x2="340" y2="130" stroke="var(--border)" stroke-width="1"/>
                <rect x="20" y="45" width="50" height="85" rx="6" fill="url(#barGrad1)" opacity="0.9"/>
                <rect x="90" y="70" width="50" height="60" rx="6" fill="url(#barGrad3)" opacity="0.9"/>
                <rect x="160" y="30" width="50" height="100" rx="6" fill="url(#barGrad2)" opacity="0.9"/>
                <rect x="230" y="100" width="50" height="30" rx="6" fill="url(#barGrad4)" opacity="0.9"/>
                <text x="45" y="42" font-size="11" fill="var(--text)" text-anchor="middle" font-weight="700">68%</text>
                <text x="115" y="67" font-size="11" fill="var(--text)" text-anchor="middle" font-weight="700">32%</text>
                <text x="185" y="27" font-size="11" fill="var(--text)" text-anchor="middle" font-weight="700">81%</text>
                <text x="255" y="97" font-size="11" fill="var(--text)" text-anchor="middle" font-weight="700">12%</text>
            </svg>
            <div class="chart-labels"><span>Alpha</span><span>Beta</span><span>Gamma</span><span>Delta</span></div>
        </div>
        <div class="card">
            <div class="section-title" style="font-size:14px;margin-bottom:14px">Participation Distribution</div>
            <div style="display:flex;align-items:center;gap:20px">
                <svg viewBox="0 0 120 120" width="120" height="120">
                    <circle cx="60" cy="60" r="50" fill="none" stroke="var(--border)" stroke-width="20"/>
                    <circle cx="60" cy="60" r="50" fill="none" stroke="#2563EB" stroke-width="20" stroke-dasharray="188 126" stroke-dashoffset="0" transform="rotate(-90 60 60)"/>
                    <circle cx="60" cy="60" r="50" fill="none" stroke="#10B981" stroke-width="20" stroke-dasharray="75 239" stroke-dashoffset="-188" transform="rotate(-90 60 60)"/>
                    <circle cx="60" cy="60" r="50" fill="none" stroke="#F59E0B" stroke-width="20" stroke-dasharray="38 276" stroke-dashoffset="-263" transform="rotate(-90 60 60)"/>
                    <text x="60" y="65" text-anchor="middle" font-size="13" font-weight="800" fill="var(--text)">100%</text>
                </svg>
                <div class="pie-legend">
                    <div class="pie-legend-item"><div class="pie-dot" style="background:#2563EB"></div><span>Alpha — 60%</span></div>
                    <div class="pie-legend-item"><div class="pie-dot" style="background:#10B981"></div><span>Gamma — 24%</span></div>
                    <div class="pie-legend-item"><div class="pie-dot" style="background:#F59E0B"></div><span>Beta — 12%</span></div>
                    <div class="pie-legend-item"><div class="pie-dot" style="background:var(--border)"></div><span>Delta — 4%</span></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

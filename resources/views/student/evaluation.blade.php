@extends('layouts.app')

@section('breadcrumb', 'Peer Evaluation')

@section('content')
<div class="page active" id="page-evaluation">
    <div class="section-header">
        <div><div class="section-title">Peer Evaluation</div><div class="section-sub">Rate your teammates for Milestone 2</div></div>
        <span class="badge badge-amber"><i class="uil uil-clock me-1"></i> Due: Jun 5, 2025</span>
    </div>
    <div class="card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
            <div style="font-size:13.5px;font-weight:600">Group Alpha — Milestone 2 Evaluation</div>
            <div class="progress-wrap" style="width:200px;margin:0"><div class="progress-label"><span>Completed</span><span>2/4</span></div><div class="progress-bar"><div class="progress-fill" style="width:50%"></div></div></div>
        </div>
        <div class="eval-member">
            <div class="eval-av">AK</div>
            <div class="eval-info"><div class="eval-name">Aisha Kamau</div><div class="eval-role">Backend Developer</div></div>
            <div class="eval-sliders">
                <div class="slider-row"><label>Contribution</label><input type="range" min="1" max="10" value="9" oninput="this.nextElementSibling.textContent=this.value"/><span>9</span></div>
                <div class="slider-row"><label>Teamwork</label><input type="range" min="1" max="10" value="8" oninput="this.nextElementSibling.textContent=this.value"/><span>8</span></div>
                <div class="slider-row"><label>Quality</label><input type="range" min="1" max="10" value="9" oninput="this.nextElementSibling.textContent=this.value"/><span>9</span></div>
            </div>
            <span class="badge badge-green" style="margin-left:12px"><i class="uil uil-check-circle me-1"></i> Rated</span>
        </div>
        <div class="eval-member">
            <div class="eval-av" style="background:linear-gradient(135deg,var(--secondary),#84CC16)">LM</div>
            <div class="eval-info"><div class="eval-name">Leon Mensah</div><div class="eval-role">Frontend Developer</div></div>
            <div class="eval-sliders">
                <div class="slider-row"><label>Contribution</label><input type="range" min="1" max="10" value="7" oninput="this.nextElementSibling.textContent=this.value"/><span>7</span></div>
                <div class="slider-row"><label>Teamwork</label><input type="range" min="1" max="10" value="8" oninput="this.nextElementSibling.textContent=this.value"/><span>8</span></div>
                <div class="slider-row"><label>Quality</label><input type="range" min="1" max="10" value="7" oninput="this.nextElementSibling.textContent=this.value"/><span>7</span></div>
            </div>
            <span class="badge badge-green" style="margin-left:12px"><i class="uil uil-check-circle me-1"></i> Rated</span>
        </div>
        <div class="eval-member">
            <div class="eval-av" style="background:linear-gradient(135deg,var(--accent),var(--danger))">SR</div>
            <div class="eval-info"><div class="eval-name">Sara Rodriguez</div><div class="eval-role">UI/UX Designer</div></div>
            <div class="eval-sliders">
                <div class="slider-row"><label>Contribution</label><input type="range" min="1" max="10" value="5" oninput="this.nextElementSibling.textContent=this.value"/><span>5</span></div>
                <div class="slider-row"><label>Teamwork</label><input type="range" min="1" max="10" value="6" oninput="this.nextElementSibling.textContent=this.value"/><span>6</span></div>
                <div class="slider-row"><label>Quality</label><input type="range" min="1" max="10" value="8" oninput="this.nextElementSibling.textContent=this.value"/><span>8</span></div>
            </div>
            <span class="badge badge-amber" style="margin-left:12px"><i class="uil uil-hourglass me-1"></i> Pending</span>
        </div>
        <div class="eval-member">
            <div class="eval-av" style="background:linear-gradient(135deg,#8B5CF6,var(--primary))">TP</div>
            <div class="eval-info"><div class="eval-name">Taiwo Peters</div><div class="eval-role">DevOps Engineer</div></div>
            <div class="eval-sliders">
                <div class="slider-row"><label>Contribution</label><input type="range" min="1" max="10" value="6" oninput="this.nextElementSibling.textContent=this.value"/><span>6</span></div>
                <div class="slider-row"><label>Teamwork</label><input type="range" min="1" max="10" value="7" oninput="this.nextElementSibling.textContent=this.value"/><span>7</span></div>
                <div class="slider-row"><label>Quality</label><input type="range" min="1" max="10" value="6" oninput="this.nextElementSibling.textContent=this.value"/><span>6</span></div>
            </div>
            <span class="badge badge-amber" style="margin-left:12px"><i class="uil uil-hourglass me-1"></i> Pending</span>
        </div>
        <div style="margin-top:18px;display:flex;justify-content:flex-end;gap:8px">
            <button class="btn btn-outline">Save Draft</button>
            <button class="btn btn-primary" onclick="toast('Evaluation submitted!','<i class=\'uil uil-check-circle\'></i>')"><i class="uil uil-check-circle me-1"></i> Submit Evaluation</button>
        </div>
    </div>
</div>
@endsection

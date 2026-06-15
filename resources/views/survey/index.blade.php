@extends('layouts.app')

@php
    $mode = $mode ?? 'student';
    $isSupervisor = $mode === 'supervisor';
    $studentSkills = ['PHP', 'JavaScript', 'Python', 'Java', 'React', 'Vue.js', 'Node.js', 'MySQL', 'Git', 'Docker', 'HTML/CSS'];
    $studentInterests = ['Web Development', 'Mobile Apps', 'Data Science', 'Machine Learning', 'UI/UX Design'];
    $professionalismOptions = ['Software Engineering', 'Web Development', 'Database Management', 'Project Management', 'Artificial Intelligence', 'Cybersecurity', 'Cloud Computing', 'Data Science', 'Mobile Development', 'Quality Assurance', 'UI/UX Design', 'DevOps'];
    $selectedProfessionalism = is_array($survey?->specializations) ? $survey->specializations : [];
    $completed = $isSupervisor
        ? (($survey?->years_of_experience !== null) && !empty($survey?->bio) && !empty($selectedProfessionalism))
        : (bool) ($survey && $survey->completed_at);
    $customProfessionalism = collect($selectedProfessionalism)
        ->reject(fn ($item) => in_array($item, $professionalismOptions, true))
        ->implode(', ');
@endphp

@section('breadcrumb', $isSupervisor ? 'Professionalism Survey' : 'Skills Survey')

@section('content')
<div class="page active">
    <div class="section-header">
        <div>
            <div class="section-title">{{ $isSupervisor ? 'Professionalism Survey' : 'Skills & Preferences Survey' }}</div>
            <div class="section-sub">{{ $isSupervisor ? 'Describe your professional expertise and add custom professionalism areas if they are not listed.' : 'Help us create the perfect groups for you!' }}</div>
        </div>
    </div>

    <div class="card" style="max-width: 860px; margin: 0 auto;">
        @if($completed)
        <div style="text-align: center; padding: 40px;">
            <div style="font-size: 48px; color: var(--success); margin-bottom: 20px;">
                <i class="uil uil-check-circle"></i>
            </div>
            <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 10px;">{{ $isSupervisor ? 'Professionalism Survey Completed!' : 'Survey Completed!' }}</h3>
            <p style="color: var(--text-muted); margin-bottom: 24px;">{{ $isSupervisor ? 'Your professionalism details are saved and can be updated any time.' : 'Thank you for completing the skills survey. Your information will help us in group formation.' }}</p>
            <button class="btn btn-outline btn-sm" onclick="document.getElementById('surveyForm').style.display='block'; this.parentElement.style.display='none'">
                <i class="uil uil-edit me-1"></i> Retake Survey
            </button>
        </div>
        @endif

        <form id="surveyForm" style="{{ $completed ? 'display: none;' : '' }}">
            @csrf

            @if($isSupervisor)
                <div style="margin-bottom: 24px;">
                    <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 12px; display: flex; align-items: center;">
                        <i class="uil uil-briefcase-alt me-2" style="color: var(--primary);"></i> Professionalism Areas
                    </h3>
                    <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 12px;">Select your professionalism and specialization areas. If not listed, add your own below.</p>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 10px;">
                        @foreach($professionalismOptions as $item)
                        <label style="display: flex; align-items: center; padding: 10px; border: 1px solid var(--border); border-radius: 8px; cursor: pointer;">
                            <input type="checkbox" name="specializations[]" value="{{ $item }}" style="margin-right: 10px;"
                                {{ in_array($item, $selectedProfessionalism, true) ? 'checked' : '' }}>
                            <span style="font-size: 13px;">{{ $item }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div style="margin-bottom: 24px;">
                    <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 12px; display: flex; align-items: center;">
                        <i class="uil uil-plus-circle me-2" style="color: var(--primary);"></i> Add Other Professionalism
                    </h3>
                    <textarea name="custom_specializations" rows="3" class="form-control" placeholder="If your professionalism is not listed, add it here. Separate multiple items with commas or new lines.">{{ $customProfessionalism }}</textarea>
                </div>

                <div style="margin-bottom: 24px;">
                    <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 12px; display: flex; align-items: center;">
                        <i class="uil uil-history me-2" style="color: var(--primary);"></i> Years of Experience
                    </h3>
                    <input type="number" min="0" max="60" name="years_of_experience" class="form-control" required value="{{ old('years_of_experience', $survey?->years_of_experience) }}" placeholder="e.g. 5">
                </div>

                <div style="margin-bottom: 24px;">
                    <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 12px; display: flex; align-items: center;">
                        <i class="uil uil-user-square me-2" style="color: var(--primary);"></i> Professional Bio
                    </h3>
                    <textarea name="bio" rows="5" class="form-control" placeholder="Describe your professional expertise, supervision strengths, and academic/professional background." required>{{ old('bio', $survey?->bio) }}</textarea>
                </div>
            @else
                <div style="margin-bottom: 24px;">
                    <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 12px; display: flex; align-items: center;">
                        <i class="uil uil-lightbulb me-2" style="color: var(--primary);"></i> Technical Skills
                    </h3>
                    <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 12px;">Select all skills you're comfortable with:</p>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 10px;">
                        @foreach($studentSkills as $skill)
                        <label style="display: flex; align-items: center; padding: 10px; border: 1px solid var(--border); border-radius: 8px; cursor: pointer; transition: background 0.2s;">
                            <input type="checkbox" name="skills[]" value="{{ $skill }}" style="margin-right: 10px;"
                                {{ $survey && is_array($survey->skills) && in_array($skill, $survey->skills) ? 'checked' : '' }}>
                            <span style="font-size: 13px;">{{ $skill }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div style="margin-bottom: 24px;">
                    <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 12px; display: flex; align-items: center;">
                        <i class="uil uil-bolt me-2" style="color: var(--primary);"></i> Experience Level
                    </h3>
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px;">
                        @foreach(['beginner' => 'Beginner', 'intermediate' => 'Intermediate', 'advanced' => 'Advanced'] as $val => $label)
                        <label style="display: flex; flex-direction: column; padding: 12px; border: 1px solid var(--border); border-radius: 8px; cursor: pointer;">
                            <input type="radio" name="experience_level" value="{{ $val }}" style="margin-bottom: 8px;" required
                                {{ $survey && $survey->experience_level === $val ? 'checked' : '' }}>
                            <span style="font-size: 13px; font-weight: 600;">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div style="margin-bottom: 24px;">
                    <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 12px; display: flex; align-items: center;">
                        <i class="uil uil-heart me-2" style="color: var(--primary);"></i> Project Interests
                    </h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 10px;">
                        @foreach($studentInterests as $interest)
                        <label style="display: flex; align-items: center; padding: 10px; border: 1px solid var(--border); border-radius: 8px; cursor: pointer;">
                            <input type="checkbox" name="interests[]" value="{{ $interest }}" style="margin-right: 10px;"
                                {{ $survey && is_array($survey->interests) && in_array($interest, $survey->interests) ? 'checked' : '' }}>
                            <span style="font-size: 13px;">{{ $interest }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div style="margin-bottom: 24px;">
                    <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 12px; display: flex; align-items: center;">
                        <i class="uil uil-target me-2" style="color: var(--primary);"></i> Your Goals
                    </h3>
                    <textarea name="goals" rows="4" class="form-control" placeholder="What do you hope to achieve through this project?" required>{{ $survey ? $survey->goals : '' }}</textarea>
                </div>
            @endif

            <div style="display: flex; justify-content: center;">
                <button type="submit" class="btn btn-primary" style="padding: 12px 32px;">
                    <i class="uil uil-check me-2"></i> Submit Survey
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('surveyForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch("{{ route('survey.store') }}", {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(async response => {
        const data = await response.json().catch(() => ({}));
        if (!response.ok) {
            throw data;
        }
        return data;
    })
    .then(data => {
        toast(data.message, '<i class="uil uil-check"></i>');
        setTimeout(() => {
            window.location.reload();
        }, 1500);
    })
    .catch(error => {
        console.error('Error:', error);
        toast(error.message || 'An error occurred. Please try again.', '<i class="uil uil-exclamation-triangle"></i>');
    });
});
</script>
@endsection

@extends('layouts.app')

@section('breadcrumb', 'Skills Survey')

@section('content')
<div class="page active">
    <div class="section-header">
        <div>
            <div class="section-title">Skills & Preferences Survey</div>
            <div class="section-sub">Help us create the perfect groups for you!</div>
        </div>
    </div>

    <div class="card" style="max-width: 800px; margin: 0 auto;">
        @if($survey && $survey->completed_at)
        <div style="text-align: center; padding: 40px;">
            <div style="font-size: 48px; color: var(--success); margin-bottom: 20px;">
                <i class="uil uil-check-circle"></i>
            </div>
            <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 10px;">Survey Completed!</h3>
            <p style="color: var(--text-muted); margin-bottom: 24px;">Thank you for completing the skills survey. Your information will help us in group formation.</p>
            <button class="btn btn-outline btn-sm" onclick="document.getElementById('surveyForm').style.display='block'; this.parentElement.style.display='none'">
                <i class="uil uil-edit me-1"></i> Retake Survey
            </button>
        </div>
        @endif

        <form id="surveyForm" style="{{ $survey && $survey->completed_at ? 'display: none;' : '' }}">
            @csrf
            <!-- Skills Section -->
            <div style="margin-bottom: 24px;">
                <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 12px; display: flex; align-items: center;">
                    <i class="uil uil-lightbulb me-2" style="color: var(--primary);"></i> Technical Skills
                </h3>
                <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 12px;">Select all skills you're comfortable with:</p>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 10px;">
                    @foreach(['PHP', 'JavaScript', 'Python', 'Java', 'React', 'Vue.js', 'Node.js', 'MySQL', 'Git', 'Docker', 'HTML/CSS'] as $skill)
                    <label style="display: flex; align-items: center; padding: 10px; border: 1px solid var(--border); border-radius: 8px; cursor: pointer; transition: background 0.2s;">
                        <input type="checkbox" name="skills[]" value="{{ $skill }}" style="margin-right: 10px;" 
                            {{ $survey && is_array($survey->skills) && in_array($skill, $survey->skills) ? 'checked' : '' }}>
                        <span style="font-size: 13px;">{{ $skill }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <!-- Experience Level -->
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

            <!-- Interests -->
            <div style="margin-bottom: 24px;">
                <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 12px; display: flex; align-items: center;">
                    <i class="uil uil-heart me-2" style="color: var(--primary);"></i> Project Interests
                </h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 10px;">
                    @foreach(['Web Development', 'Mobile Apps', 'Data Science', 'Machine Learning', 'UI/UX Design'] as $interest)
                    <label style="display: flex; align-items: center; padding: 10px; border: 1px solid var(--border); border-radius: 8px; cursor: pointer;">
                        <input type="checkbox" name="interests[]" value="{{ $interest }}" style="margin-right: 10px;"
                            {{ $survey && is_array($survey->interests) && in_array($interest, $survey->interests) ? 'checked' : '' }}>
                        <span style="font-size: 13px;">{{ $interest }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <!-- Goals -->
            <div style="margin-bottom: 24px;">
                <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 12px; display: flex; align-items: center;">
                    <i class="uil uil-target me-2" style="color: var(--primary);"></i> Your Goals
                </h3>
                <textarea name="goals" rows="4" class="form-control" placeholder="What do you hope to achieve through this project?" required>{{ $survey ? $survey->goals : '' }}</textarea>
            </div>

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
    .then(response => response.json())
    .then(data => {
        toast(data.message, '<i class="uil uil-check"></i>');
        setTimeout(() => {
            window.location.reload();
        }, 1500);
    })
    .catch(error => {
        console.error('Error:', error);
        toast('An error occurred. Please try again.', '<i class="uil uil-exclamation-triangle"></i>');
    });
});
</script>
@endsection

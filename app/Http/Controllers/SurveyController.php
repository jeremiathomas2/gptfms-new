<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SupervisorProfile;
use App\Models\StudentSkillsSurvey;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SurveyController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('supervisor')) {
            $survey = SupervisorProfile::getByUserId($user->id);

            return view('survey.index', [
                'mode' => 'supervisor',
                'survey' => $survey,
            ]);
        }

        $survey = StudentSkillsSurvey::getByUser($user->id);

        return view('survey.index', [
            'mode' => 'student',
            'survey' => $survey,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->hasRole('supervisor')) {
            $validated = $request->validate([
                'specializations' => 'nullable|array',
                'specializations.*' => 'string|max:255',
                'custom_specializations' => 'nullable|string|max:1000',
                'years_of_experience' => 'required|integer|min:0|max:60',
                'bio' => 'required|string|max:3000',
            ]);

            $specializations = collect($validated['specializations'] ?? [])
                ->map(fn ($item) => trim((string) $item))
                ->filter()
                ->values();

            $custom = collect(preg_split('/[\r\n,]+/', (string) ($validated['custom_specializations'] ?? '')))
                ->map(fn ($item) => trim((string) $item))
                ->filter()
                ->values();

            $merged = $specializations
                ->concat($custom)
                ->unique(fn ($item) => mb_strtolower($item))
                ->values()
                ->all();

            if (count($merged) === 0) {
                throw ValidationException::withMessages([
                    'specializations' => 'Select at least one professionalism area or add a custom one.',
                ]);
            }

            SupervisorProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'specializations' => $merged,
                    'years_of_experience' => (int) $validated['years_of_experience'],
                    'bio' => $validated['bio'],
                    'is_available' => true,
                    'last_activity_at' => now(),
                ]
            );

            return response()->json(['message' => 'Professionalism survey submitted successfully!']);
        }

        $validated = $request->validate([
            'skills' => 'required|array',
            'experience_level' => 'required|string',
            'interests' => 'required|array',
            'goals' => 'required|string',
        ]);

        StudentSkillsSurvey::updateOrCreate(
            ['user_id' => $user->id],
            [
                'skills' => $validated['skills'],
                'experience_level' => $validated['experience_level'],
                'interests' => $validated['interests'],
                'goals' => $validated['goals'],
                'completed_at' => now(),
            ]
        );

        return response()->json(['message' => 'Survey submitted successfully!']);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentSkillsSurvey;
use Illuminate\Support\Facades\Auth;

class SurveyController extends Controller
{
    public function index()
    {
        $survey = StudentSkillsSurvey::getByUser(Auth::id());
        return view('survey.index', compact('survey'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'skills' => 'required|array',
            'experience_level' => 'required|string',
            'interests' => 'required|array',
            'goals' => 'required|string',
        ]);

        StudentSkillsSurvey::updateOrCreate(
            ['user_id' => Auth::id()],
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

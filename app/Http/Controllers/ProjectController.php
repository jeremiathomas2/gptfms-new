<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with(['group', 'supervisor'])->paginate(10);
        return view('projects.index', compact('projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'course_code' => 'required|string|max:20',
            'description' => 'required|string',
            'deadline' => 'required|date',
            'supervisor_id' => 'nullable|exists:users,id',
        ]);

        $project = Project::create([
            'title' => $validated['title'],
            'course_code' => $validated['course_code'],
            'description' => $validated['description'],
            'deadline' => $validated['deadline'],
            'supervisor_id' => $validated['supervisor_id'],
            'status' => 'planning',
            'progress_percentage' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Project created successfully!',
            'project' => $project
        ]);
    }
}

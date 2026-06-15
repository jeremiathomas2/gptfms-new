<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ProfileController extends Controller
{
    public function settings()
    {
        $user = Auth::user()?->load(['studentSkillsSurvey', 'supervisorProfile']);
        return view('profile.settings', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'bio' => 'nullable|string|max:1000',
            'skills' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'],
            'last_name' => $validated['last_name'],
            'name' => trim($validated['first_name'] . ' ' . ($validated['middle_name'] ? $validated['middle_name'] . ' ' : '') . $validated['last_name']),
            'email' => $validated['email'],
            'bio' => $validated['bio'],
            'skills' => $validated['skills'],
        ];

        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $filename = time() . '.' . $avatar->getClientOriginalExtension();
            $avatar->move(public_path('uploads/avatars'), $filename);
            $data['avatar'] = 'uploads/avatars/' . $filename;
        }

        $user->update($data);

        return response()->json([
            'message' => 'Profile updated successfully!', 
            'user' => $user,
            'avatar_url' => isset($data['avatar']) ? asset($data['avatar']) : null
        ]);
    }

    public function updateSecurity(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json(['message' => 'Current password does not match our records.'], 422);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json(['message' => 'Password updated successfully!']);
    }
}

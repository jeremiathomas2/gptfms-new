<?php

namespace App\Http\Controllers;

use App\Models\GroupSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupSettingsController extends Controller
{
    public function index()
    {
        $settings = GroupSetting::first() ?? new GroupSetting();
        return view('groups.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'participants_per_group' => 'required|integer|min:1',
            'countdown_minutes' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'auto_create_groups' => 'boolean',
            'balance_by_gender' => 'boolean',
            'balance_by_skills' => 'boolean',
        ]);

        $settings = GroupSetting::first();
        
        if (!$settings) {
            $settings = new GroupSetting();
            $settings->created_by = Auth::id();
        }

        $settings->fill($validated);
        
        // Handle countdown end time
        if ($request->has('is_active') && $request->is_active) {
            if (!$settings->is_active || $settings->countdown_minutes != $validated['countdown_minutes']) {
                $settings->countdown_end_time = now()->addMinutes($validated['countdown_minutes']);
            }
        } else {
            $settings->countdown_end_time = null;
        }

        $settings->is_active = $request->has('is_active');
        $settings->auto_create_groups = $request->has('auto_create_groups');
        $settings->balance_by_gender = $request->has('balance_by_gender');
        $settings->balance_by_skills = $request->has('balance_by_skills');
        $settings->updated_by = Auth::id();
        
        $settings->save();

        return redirect()->back()->with('success', 'Group settings updated successfully.');
    }
}

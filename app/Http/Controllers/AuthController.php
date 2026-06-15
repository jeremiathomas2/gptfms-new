<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        if (!SystemSetting::getBool('auth.login_enabled', true)) {
            return back()
                ->withInput($request->only('email'))
                ->with('error', 'Login is temporarily disabled by the administrator.');
        }

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (!SystemSetting::getBool('auth.registration_enabled', true)) {
            return redirect()->route('login')->with('error', 'Registration is temporarily disabled by the administrator.');
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        if (!SystemSetting::getBool('auth.registration_enabled', true)) {
            return redirect()->route('login')->with('error', 'Registration is temporarily disabled by the administrator.');
        }

        // Validate registration data for GPTFMS system
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'gender' => 'required|in:male,female,other',
            'role' => 'required|in:student,supervisor',
            'password' => 'required|string|min:8|confirmed',
            'terms' => 'accepted',
        ];
        
        $messages = [
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'email.required' => 'Email address is required.',
            'email.unique' => 'This email address is already registered.',
            'phone.required' => 'Phone number is required.',
            'gender.required' => 'Please select your gender.',
            'role.required' => 'Please select your user type.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.confirmed' => 'Password confirmation does not match.',
            'terms.accepted' => 'You must accept the terms and conditions.'
        ];
        
        // Add registration number validation only for students
        if ($request->input('role') === 'student') {
            $rules['registration_number'] = 'required|string|max:50|unique:users,registration_number';
            $messages['registration_number.required'] = 'Registration number is required for students.';
            $messages['registration_number.unique'] = 'This registration number is already registered.';
        }
        
        $validated = $request->validate($rules, $messages);

        try {
            // Create user in database with GPTFMS requirements
            $userData = [
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'gender' => $validated['gender'],
                'password' => Hash::make($validated['password']),
                'status' => 'active',
                'last_login_ip' => $request->ip(),
            ];
            
            // Add registration number only for students
            if ($validated['role'] === 'student') {
                $userData['registration_number'] = $validated['registration_number'];
            } else {
                // For supervisors, generate a unique registration number
                $userData['registration_number'] = 'SUP_' . time() . '_' . rand(1000, 9999);
            }
            
            $user = User::create($userData);

            // Assign role using Spatie Permission package
            $user->assignRole($validated['role']);

            Auth::login($user);

            // Redirect students to survey, supervisors to profile completion
            if ($validated['role'] === 'student') {
                return redirect()->route('survey.index')
                    ->with('success', 'Registration successful! Please complete the skills assessment survey.');
            } else {
                $user->supervisorProfile()->firstOrCreate(
                    ['user_id' => $user->id],
                    ['is_available' => true]
                );

                return redirect()->route('survey.index')
                    ->with('success', 'Registration successful! Please complete the professionalism survey.');
            }

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['registration' => 'Registration failed: ' . $e->getMessage()]);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}

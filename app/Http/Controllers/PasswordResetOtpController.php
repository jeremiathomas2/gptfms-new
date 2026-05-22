<?php

namespace App\Http\Controllers;

use App\Models\PasswordResetOtp;
use App\Models\User;
use App\Notifications\PasswordResetOtpNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class PasswordResetOtpController extends Controller
{
    public function showRequest()
    {
        return view('auth.forgot-password');
    }

    public function sendOtp(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $email = strtolower(trim($validated['email']));
        $key = 'pwd-otp:' . $request->ip() . ':' . sha1($email);

        if (RateLimiter::tooManyAttempts($key, 5)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Too many requests. Please try again later.'], 429);
            }
            return back()->withErrors(['email' => 'Too many requests. Please try again later.'])->withInput();
        }

        RateLimiter::hit($key, 60);

        $user = User::where('email', $email)->first();
        $otp = (string) random_int(100000, 999999);
        $minutesValid = 10;

        PasswordResetOtp::where('email', $email)
            ->whereNull('consumed_at')
            ->delete();

        $record = PasswordResetOtp::create([
            'user_id' => $user?->id,
            'email' => $email,
            'otp_hash' => Hash::make($otp),
            'attempts' => 0,
            'expires_at' => now()->addMinutes($minutesValid),
        ]);

        if ($user) {
            $user->notify(new PasswordResetOtpNotification($otp, $minutesValid));
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'If this email exists, an OTP has been sent.']);
        }

        return redirect()
            ->route('password.reset', ['email' => $email])
            ->with('status', 'If this email exists, an OTP has been sent.');
    }

    public function showReset(Request $request)
    {
        $email = $request->query('email') ?: old('email');
        return view('auth.reset-password', ['email' => $email]);
    }

    public function verifyOtp(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|min:4|max:10',
        ]);

        $email = strtolower(trim($validated['email']));
        $otp = trim($validated['otp']);

        $record = PasswordResetOtp::where('email', $email)
            ->whereNull('consumed_at')
            ->orderByDesc('id')
            ->first();

        if (!$record || $record->isExpired()) {
            return response()->json(['message' => 'OTP expired. Please request a new one.'], 422);
        }

        if ($record->attempts >= 5) {
            return response()->json(['message' => 'Too many attempts. Please request a new OTP.'], 422);
        }

        $record->increment('attempts');

        if (!Hash::check($otp, $record->otp_hash)) {
            return response()->json(['message' => 'Invalid OTP. Please try again.'], 422);
        }

        $token = Str::random(64);
        $record->update([
            'reset_token_hash' => Hash::make($token),
            'reset_token_expires_at' => now()->addMinutes(15),
        ]);

        return response()->json([
            'message' => 'OTP verified.',
            'reset_token' => $token,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'reset_token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $email = strtolower(trim($validated['email']));
        $token = $validated['reset_token'];

        $record = PasswordResetOtp::where('email', $email)
            ->whereNull('consumed_at')
            ->orderByDesc('id')
            ->first();

        if (!$record || !$record->reset_token_hash || !$record->reset_token_expires_at || $record->reset_token_expires_at->isPast()) {
            return response()->json(['message' => 'Reset session expired. Please verify OTP again.'], 422);
        }

        if (!Hash::check($token, $record->reset_token_hash)) {
            return response()->json(['message' => 'Invalid reset session. Please verify OTP again.'], 422);
        }

        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json(['message' => 'No user found for this email.'], 422);
        }

        $user->update(['password' => Hash::make($validated['password'])]);

        $record->update([
            'consumed_at' => now(),
            'reset_token_hash' => null,
            'reset_token_expires_at' => null,
        ]);

        return response()->json(['message' => 'Password reset successfully.']);
    }
}

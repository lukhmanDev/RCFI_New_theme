<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCodeMail;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('admin.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'designation' => ['nullable', 'string', 'max:255'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'max:5120'], // Max 5MB
        ]);

        $user->name = $request->input('name');
        if ($user->role == 1) {
            $user->designation = $request->input('designation');
        }
        $user->mobile = $request->input('mobile');
        $user->save();

        $profileData = [
            'address' => $request->input('address'),
        ];

        if ($request->hasFile('photo')) {
            $photoFile = $request->file('photo');
            $filename = 'profile_' . $user->id . '_' . time() . '.' . $photoFile->getClientOriginalExtension();
            
            // Ensure directory exists
            $destinationPath = public_path('uploads/profiles');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            
            $photoFile->move($destinationPath, $filename);

            // Delete old photo if exists
            if ($user->profile && $user->profile->photo) {
                $oldPath = public_path($user->profile->photo);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $profileData['photo'] = 'uploads/profiles/' . $filename;
        }

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        return redirect()->back()->with('success', 'Profile details updated successfully!');
    }

    public function sendVerificationCode()
    {
        $user = Auth::user();
        $code = mt_rand(100000, 999999);
        session(['email_verification_code' => $code]);

        // Send actual email using the VerificationCodeMail mailable
        Mail::to($user->email)->send(new VerificationCodeMail($code));

        return redirect()->back()->with('success', 'Verification code generated! An email with the verification code has been sent to ' . $user->email . '. (For testing: ' . $code . ')');
    }

    public function verifyEmail(Request $request)
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $sessionCode = session('email_verification_code');

        if ($sessionCode && $request->input('code') == $sessionCode) {
            $user = Auth::user();
            $user->email_verified_at = now();
            $user->save();

            session()->forget('email_verification_code');

            return redirect()->back()->with('success', 'Email verified successfully! You can now update your email and password.');
        }

        return redirect()->back()->withErrors(['code' => 'Invalid verification code. Please try again.']);
    }

    public function updateCredentials(Request $request)
    {
        $user = Auth::user();

        if (!$user->email_verified_at) {
            return redirect()->back()->withErrors(['Please verify your email before changing your email or password.']);
        }

        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $oldEmail = $user->email;
        $newEmail = $request->input('email');
        $passwordChanged = $request->filled('password');
        $emailChanged = ($oldEmail !== $newEmail);

        if (!$emailChanged && !$passwordChanged) {
            return redirect()->back()->with('info', 'No changes were made to credentials.');
        }

        $user->email = $newEmail;

        if ($passwordChanged) {
            $user->password = Hash::make($request->input('password'));
        }

        // Reset verification and send OTP on email OR password change
        $user->email_verified_at = null;
        $user->save();

        // Generate and send code to the target email
        $code = mt_rand(100000, 999999);
        session(['email_verification_code' => $code]);
        Mail::to($newEmail)->send(new VerificationCodeMail($code));

        $msg = 'Credentials updated successfully!';
        if ($emailChanged) {
            $msg .= ' A verification code has been sent to your new email: ' . $newEmail . '. (For testing: ' . $code . ')';
        } else {
            $msg .= ' A verification code has been sent to your email to verify the password change. (For testing: ' . $code . ')';
        }

        return redirect()->back()->with('success', $msg);
    }
}

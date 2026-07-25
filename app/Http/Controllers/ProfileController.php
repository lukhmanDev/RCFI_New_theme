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
            'photo' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,svg,webp,avif', 'max:10240'], // Max 10MB
        ]);

        $user->name = $request->input('name');
        if ($user->isSuperAdmin()) {
            $user->designation = $request->input('designation');
        }
        $user->mobile = $request->input('mobile');
        $user->save();

        $profileData = [
            'address' => $request->input('address'),
        ];

        if ($request->hasFile('photo')) {
            $photoFile = $request->file('photo');
            $ext = strtolower($photoFile->getClientOriginalExtension());
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif'])) {
                $ext = 'jpg';
            }
            $filename = 'profile_' . $user->id . '_' . time() . '.' . $ext;
            $targetPath = public_path('uploads/profiles/' . $filename);
            
            $this->compressAndSaveImage($photoFile, $targetPath, 2 * 1024 * 1024);

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

        return redirect()->back()->with('success', 'Verification code generated! An email with the verification code has been sent to ' . $user->email . '.');
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
            $msg .= ' A verification code has been sent to your new email: ' . $newEmail . '.';
        } else {
            $msg .= ' A verification code has been sent to your email to verify the password change.';
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Compress and save uploaded image if size exceeds 2 MB (or has large dimensions).
     */
    private function compressAndSaveImage($file, $destinationPath, $maxSizeBytes = 2097152)
    {
        $tempPath = $file->getRealPath();
        $fileSize = filesize($tempPath);

        $dir = dirname($destinationPath);
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $imageInfo = @getimagesize($tempPath);
        if (!$imageInfo) {
            return $file->move($dir, basename($destinationPath));
        }

        $mime = $imageInfo['mime'] ?? '';
        $width = $imageInfo[0] ?? 0;
        $height = $imageInfo[1] ?? 0;

        if ($fileSize <= $maxSizeBytes && $width <= 2000 && $height <= 2000) {
            return $file->move($dir, basename($destinationPath));
        }

        $srcImage = null;
        switch ($mime) {
            case 'image/jpeg':
                $srcImage = @imagecreatefromjpeg($tempPath);
                break;
            case 'image/png':
                $srcImage = @imagecreatefrompng($tempPath);
                break;
            case 'image/webp':
                $srcImage = @imagecreatefromwebp($tempPath);
                break;
            case 'image/avif':
                $srcImage = function_exists('imagecreatefromavif') ? @imagecreatefromavif($tempPath) : null;
                break;
            case 'image/gif':
                $srcImage = @imagecreatefromgif($tempPath);
                break;
        }

        if (!$srcImage) {
            return $file->move($dir, basename($destinationPath));
        }

        $maxWidth = 1920;
        $maxHeight = 1920;
        $newWidth = $width;
        $newHeight = $height;

        if ($newWidth > $maxWidth || $newHeight > $maxHeight) {
            $ratio = min($maxWidth / $newWidth, $maxHeight / $newHeight);
            $newWidth = (int)round($newWidth * $ratio);
            $newHeight = (int)round($newHeight * $ratio);
        }

        $destImage = imagecreatetruecolor($newWidth, $newHeight);

        if ($mime === 'image/png' || $mime === 'image/webp') {
            imagealphablending($destImage, false);
            imagesavealpha($destImage, true);
            $transparent = imagecolorallocatealpha($destImage, 255, 255, 255, 127);
            imagefilledrectangle($destImage, 0, 0, $newWidth, $newHeight, $transparent);
        }

        imagecopyresampled($destImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        if ($mime === 'image/png') {
            @imagepng($destImage, $destinationPath, 6);
        } elseif ($mime === 'image/webp') {
            @imagewebp($destImage, $destinationPath, 80);
        } elseif ($mime === 'image/avif' && function_exists('imageavif')) {
            @imageavif($destImage, $destinationPath, 80);
        } else {
            @imagejpeg($destImage, $destinationPath, 80);
        }

        imagedestroy($srcImage);
        imagedestroy($destImage);

        if (file_exists($destinationPath) && filesize($destinationPath) > $maxSizeBytes) {
            $secondSrc = @imagecreatefromjpeg($destinationPath);
            if ($secondSrc) {
                @imagejpeg($secondSrc, $destinationPath, 60);
                imagedestroy($secondSrc);
            }
        }

        return true;
    }
}

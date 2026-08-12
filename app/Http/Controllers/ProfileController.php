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

        if (!$user) {
            return redirect()->back()->with('error', 'Unauthenticated.');
        }

        // If user is not SuperAdmin, process photo upload if provided
        if (!$user->isSuperAdmin()) {
            if (!$request->hasFile('photo')) {
                return redirect()->back()->with('error', 'Profile text details can only be modified by Super Admin. You can update your profile photo.');
            }

            $request->validate([
                'photo' => ['required', 'file', 'mimes:jpeg,png,jpg,gif,svg,webp,avif', 'max:10240'],
            ]);

            $photoFile = $request->file('photo');
            $ext = strtolower($photoFile->getClientOriginalExtension());
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif'])) {
                $ext = 'jpg';
            }
            $filename = 'profile_' . $user->id . '_' . time() . '.' . $ext;
            $targetPath = public_path('uploads/profiles/' . $filename);
            
            $this->compressAndSaveImage($photoFile, $targetPath, 2 * 1024 * 1024);

            if ($user->profile && $user->profile->photo) {
                $oldPath = public_path($user->profile->photo);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                ['photo' => 'uploads/profiles/' . $filename]
            );

            return redirect()->back()->with('success', 'Profile photo updated successfully!');
        }

        $validated = $request->validate([
            'name'            => ['required', 'string', 'min:2', 'max:255'],
            'designation'     => ['nullable', 'string', 'max:255'],
            'mobile'          => ['nullable', 'string', 'max:20'],
            'father_name'     => ['nullable', 'string', 'max:255'],
            'mother_name'     => ['nullable', 'string', 'max:255'],
            'date_of_birth'   => ['nullable', 'date'],
            'date_of_joining' => ['nullable', 'date'],
            'gender'          => ['nullable', 'string', 'in:Male,Female,Other'],
            'marital_status'  => ['nullable', 'string', 'in:Single,Married,Divorced,Widowed'],
            'house_name'      => ['nullable', 'string', 'max:255'],
            'place'           => ['nullable', 'string', 'max:255'],
            'po'              => ['nullable', 'string', 'max:255'],
            'district'        => ['nullable', 'string', 'max:255'],
            'state'           => ['nullable', 'string', 'max:255'],
            'pin_code'        => ['nullable', 'string', 'max:10'],
            'aadhar_number'   => ['nullable', 'string', 'max:20'],
            'pan_card_number' => ['nullable', 'string', 'max:20'],
            'account_number'  => ['nullable', 'string', 'max:30'],
            'bank_name'       => ['nullable', 'string', 'max:255'],
            'bank_branch'     => ['nullable', 'string', 'max:255'],
            'ifsc_code'       => ['nullable', 'string', 'max:20'],
            'address'         => ['nullable', 'string', 'max:1000'],
            'photo'           => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,svg,webp,avif', 'max:10240'], // Max 10MB
        ]);

        $user->name            = $validated['name'];
        if ($user->isSuperAdmin() && isset($validated['designation'])) {
            $user->designation = $validated['designation'];
        }
        $user->mobile          = $validated['mobile'] ?? $user->mobile;
        $user->father_name     = $validated['father_name'] ?? $user->father_name;
        $user->mother_name     = $validated['mother_name'] ?? $user->mother_name;
        $user->date_of_birth   = $validated['date_of_birth'] ?? $user->date_of_birth;
        if ($user->isSuperAdmin() && isset($validated['date_of_joining'])) {
            $user->date_of_joining = $validated['date_of_joining'];
        }
        $user->gender          = $validated['gender'] ?? $user->gender;
        $user->marital_status  = $validated['marital_status'] ?? $user->marital_status;
        $user->house_name      = $validated['house_name'] ?? $user->house_name;
        $user->place           = $validated['place'] ?? $user->place;
        $user->po              = $validated['po'] ?? $user->po;
        $user->district        = $validated['district'] ?? $user->district;
        $user->state           = $validated['state'] ?? $user->state;
        $user->pin_code        = $validated['pin_code'] ?? $user->pin_code;
        $user->aadhar_number   = $validated['aadhar_number'] ?? $user->aadhar_number;
        $user->pan_card_number = isset($validated['pan_card_number']) ? strtoupper($validated['pan_card_number']) : $user->pan_card_number;
        $user->account_number  = $validated['account_number'] ?? $user->account_number;
        $user->bank_name       = $validated['bank_name'] ?? $user->bank_name;
        $user->bank_branch     = $validated['bank_branch'] ?? $user->bank_branch;
        $user->ifsc_code       = isset($validated['ifsc_code']) ? strtoupper($validated['ifsc_code']) : $user->ifsc_code;
        $user->save();

        // Synthesize address string for Profile model
        $addressParts = array_filter([
            $user->house_name,
            $user->place,
            $user->po ? 'PO: ' . $user->po : null,
            $user->district,
            $user->state ? $user->state . ($user->pin_code ? ' - ' . $user->pin_code : '') : $user->pin_code,
        ]);
        $fullAddress = !empty($addressParts) ? implode(', ', $addressParts) : ($request->input('address') ?? null);

        $profileData = [
            'address' => $fullAddress,
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

        if (!$user || !$user->isSuperAdmin()) {
            return redirect()->back()->with('error', 'Unauthorized action. Only Super Admin can update credentials.');
        }

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

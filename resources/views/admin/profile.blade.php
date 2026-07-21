@extends('layouts.admin')

@section('title', 'My Profile')

@section('content')
<style>
    /* Modern Color Palette & Variables */
    :root {
        --bg-main: #f8fafc;
        --bg-card: #ffffff;
        --bg-input: #f8fafc;
        --text-primary: #1e293b;
        --text-secondary: #64748b;
        --border-color: #e2e8f0;
        --accent-primary: #6366f1;
        --accent-primary-hover: #4f46e5;
        --accent-success: #10b981;
        --accent-warning: #f59e0b;
        --accent-danger: #ef4444;
        --radius-md: 8px;
        --radius-lg: 16px;
        --transition: all 0.2s ease-in-out;
    }

    /* Layout */
    .profile-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem 1rem;
        color: var(--text-primary);
    }

    .profile-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 2rem;
        align-items: start;
    }

    @media (min-width: 992px) {
        .profile-grid {
            grid-template-columns: 320px 1fr;
        }
    }

    /* Modern Cards */
    .modern-card {
        background-color: var(--bg-card);
        border-radius: var(--radius-lg);
        border: 1px solid var(--border-color);
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.05);
        overflow: hidden;
    }

    .card-header {
        padding: 1.5rem 1.5rem 1rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .card-body {
        padding: 1.5rem;
    }

    /* Avatar Section */
    .avatar-wrapper {
        position: relative;
        width: 130px;
        height: 130px;
        margin: 0 auto 1.5rem;
    }

    .avatar-img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #ffffff;
        box-shadow: 0 0 0 2px var(--border-color);
        transition: var(--transition);
    }

    .status-indicator {
        position: absolute;
        bottom: 5px;
        right: 5px;
        width: 20px;
        height: 20px;
        background-color: var(--accent-success);
        border-radius: 50%;
        border: 3px solid #ffffff;
    }

    /* Badges */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    .badge-success { background: rgba(16, 185, 129, 0.1); color: var(--accent-success); border: 1px solid rgba(16, 185, 129, 0.2); }
    .badge-warning { background: rgba(245, 158, 11, 0.1); color: var(--accent-warning); border: 1px solid rgba(245, 158, 11, 0.2); }

    /* Form Controls */
    .form-group {
        margin-bottom: 1.25rem;
    }
    
    .modern-label {
        display: block;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-secondary);
        margin-bottom: 0.5rem;
    }

    .modern-input {
        width: 100%;
        background-color: var(--bg-input);
        border: 1px solid var(--border-color);
        color: var(--text-primary);
        padding: 0.75rem 1rem;
        border-radius: var(--radius-md);
        font-size: 0.95rem;
        transition: var(--transition);
    }

    .modern-input:focus {
        outline: none;
        border-color: var(--accent-primary);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        background-color: #ffffff;
    }

    .modern-input:disabled, .modern-input[readonly] {
        background-color: #f1f5f9;
        color: var(--text-secondary);
        cursor: not-allowed;
    }

    /* Buttons */
    .modern-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: var(--radius-md);
        font-weight: 700;
        font-size: 0.9rem;
        cursor: pointer;
        transition: var(--transition);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.15);
    }

    .modern-btn:hover:not(:disabled) {
        opacity: 0.95;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(99, 102, 241, 0.25);
    }

    .modern-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .btn-outline {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        color: #475569;
        box-shadow: none;
    }

    .btn-outline:hover:not(:disabled) {
        background: #f8fafc;
        border-color: #94a3b8;
        color: #1e293b;
        box-shadow: none;
    }

    /* Alerts */
    .alert {
        padding: 1rem 1.25rem;
        border-radius: var(--radius-md);
        margin-bottom: 1.5rem;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .alert-success { background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: #065f46; }
    .alert-danger { background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #991b1b; }

    /* Locked Overlay (Glassmorphism) */
    .locked-overlay {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, 0.4);
        backdrop-filter: blur(4px);
        z-index: 10;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border-radius: var(--radius-lg);
    }
</style>

<div class="profile-container">

    @if (session('success'))
        <div class="alert alert-success">
            <i class="bx bx-check-circle" style="font-size: 1.2rem;"></i> 
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <i class="bx bx-error-circle" style="font-size: 1.2rem;"></i>
            <ul style="margin: 0; padding-left: 1rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="profile-grid">
            
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            
            <div class="modern-card" style="text-align: center; padding: 2rem 1.5rem;">
                <div class="avatar-wrapper">
                    <img src="{{ $user->profile_photo_url }}" id="profile-avatar-preview" class="avatar-img" alt="{{ $user->name }}">
                    <div class="status-indicator"></div>
                </div>
                
                <h2 style="font-size: 1.4rem; font-weight: 700; margin: 0 0 0.25rem 0;">{{ $user->name }}</h2>
                <p style="color: var(--text-secondary); font-size: 0.95rem; margin: 0 0 1.25rem 0;">{{ $user->designation ?? 'No Designation' }}</p>
                
                @if($user->email_verified_at)
                    <span class="badge badge-success"><i class="bx bxs-badge-check"></i> Verified Email</span>
                @else
                    <span class="badge badge-warning"><i class="bx bx-info-circle"></i> Unverified Email</span>
                @endif

                @if($user->mobile || ($user->profile && $user->profile->address))
                    <div style="margin-top: 1.5rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem; text-align: left; display: flex; flex-direction: column; gap: 1rem;">
                        @if($user->mobile)
                            <div style="display: flex; align-items: center; gap: 0.75rem; color: var(--text-secondary);">
                                <div style="background: var(--bg-input); padding: 0.5rem; border-radius: 8px;"><i class="bx bx-phone" style="color: var(--accent-primary); font-size: 1.1rem;"></i></div>
                                <span style="font-size: 0.9rem;">{{ $user->mobile }}</span>
                            </div>
                        @endif
                        @if($user->profile && $user->profile->address)
                            <div style="display: flex; align-items: flex-start; gap: 0.75rem; color: var(--text-secondary);">
                                <div style="background: var(--bg-input); padding: 0.5rem; border-radius: 8px;"><i class="bx bx-map" style="color: var(--accent-success); font-size: 1.1rem;"></i></div>
                                <span style="font-size: 0.9rem; line-height: 1.5;">{{ $user->profile->address }}</span>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            @if(!$user->email_verified_at)
                <div class="modern-card" style="border-left: 4px solid var(--accent-warning);">
                    <div class="card-body">
                        <h3 class="card-title" style="color: var(--accent-warning); margin-bottom: 0.75rem;">
                            <i class="bx bx-mail-send" style="font-size: 1.3rem;"></i> Verify Identity
                        </h3>
                        <p style="color: var(--text-secondary); font-size: 0.85rem; line-height: 1.6; margin-bottom: 1.25rem;">
                            Verify your email to unlock access to edit your account credentials.
                        </p>

                        @if(!session('email_verification_code'))
                            <form action="{{ route('profile.send_code') }}" method="POST">
                                @csrf
                                <button type="submit" class="modern-btn btn-outline" style="width: 100%;">
                                    <i class="bx bx-paper-plane"></i> Send Verification Code
                                </button>
                            </form>
                        @else
                            <form action="{{ route('profile.verify') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <input type="text" class="modern-input" id="code" name="code" placeholder="------" maxlength="6" style="letter-spacing: 0.5em; text-align: center; font-size: 1.2rem; font-weight: bold; background: #000;" required>
                                </div>
                                <button type="submit" class="modern-btn" style="width: 100%; background: var(--accent-success);">
                                    Verify Code
                                </button>
                            </form>
                            
                            <form action="{{ route('profile.send_code') }}" method="POST" style="text-align: center; margin-top: 1rem;">
                                @csrf
                                <button type="submit" style="background: none; border: none; color: var(--text-secondary); font-size: 0.8rem; cursor: pointer; text-decoration: underline;">
                                    Resend Code
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            
            <div class="modern-card">
                <div class="card-header">
                    <h3 class="card-title"><i class="bx bx-user" style="color: var(--accent-primary);"></i> Profile Details</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.25rem;">
                            <div class="form-group" style="margin: 0;">
                                <label class="modern-label" for="name">Full Name</label>
                                <input type="text" class="modern-input" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            </div>
                            
                            <div class="form-group" style="margin: 0;">
                                <label class="modern-label" for="designation">Designation</label>
                                <input type="text" class="modern-input" id="designation" name="designation" value="{{ old('designation', $user->designation) }}" placeholder="e.g. Project Manager" @if(!Auth::user()->isSuperAdmin()) readonly @endif>
                            </div>
                        </div>

                        <div class="form-group" style="margin-top: 1.25rem;">
                            <label class="modern-label" for="mobile">Mobile Number</label>
                            <input type="text" class="modern-input" id="mobile" name="mobile" value="{{ old('mobile', $user->mobile) }}" placeholder="e.g. +1234567890">
                        </div>

                        <div class="form-group">
                            <label class="modern-label" for="address">Address</label>
                            <textarea class="modern-input" id="address" name="address" rows="3" style="resize: vertical;">{{ old('address', $user->profile ? $user->profile->address : '') }}</textarea>
                        </div>
                        
                        <div class="form-group">
                            <label class="modern-label" for="photo">Upload Profile Photo</label>
                            <input type="file" class="modern-input" id="photo" name="photo" accept="image/*" style="padding: 0.6rem; color: var(--text-secondary);">
                            <small style="color: var(--text-secondary); font-size: 0.75rem; margin-top: 0.4rem; display: block;">Accepted formats: JPG, PNG, WEBP. Max size 5MB.</small>
                        </div>
                        
                        <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color);">
                            <button type="submit" class="modern-btn">
                                <i class="bx bx-save"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="modern-card" style="position: relative;">
                @if(!$user->email_verified_at)
                    <div class="locked-overlay">
                        <i class="bx bxs-lock" style="font-size: 3rem; color: rgba(255,255,255,0.8); margin-bottom: 0.5rem; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.3));"></i>
                        <span style="color: white; font-weight: 600; font-size: 1.1rem;">Credentials Locked</span>
                        <span style="color: var(--text-secondary); font-size: 0.85rem; margin-top: 0.25rem;">Verify email to manage security settings</span>
                    </div>
                @endif

                <div class="card-header">
                    <h3 class="card-title"><i class="bx bx-shield-quarter" style="color: var(--accent-success);"></i> Security & Credentials</h3>
                    @if($user->email_verified_at)
                        <button type="button" id="btn-toggle-credentials" class="modern-btn btn-outline" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;">
                            <i class="bx bx-edit-alt"></i> Edit
                        </button>
                    @endif
                </div>
                
                <div class="card-body" id="credentials-form-container" style="display: {{ ($errors->has('email') || $errors->has('password') || old('email')) ? 'block' : 'none' }};">
                    <form action="{{ route('profile.update_credentials') }}" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label class="modern-label" for="email">Email Address</label>
                            <input type="email" class="modern-input" id="email" name="email" value="{{ old('email', $user->email) }}" required @if(!$user->email_verified_at) disabled @endif>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem;">
                            <div class="form-group" style="margin: 0;">
                                <label class="modern-label" for="password">New Password</label>
                                <input type="password" class="modern-input" id="password" name="password" placeholder="Leave blank to keep current" @if(!$user->email_verified_at) disabled @endif>
                            </div>
                            
                            <div class="form-group" style="margin: 0;">
                                <label class="modern-label" for="password_confirmation">Confirm Password</label>
                                <input type="password" class="modern-input" id="password_confirmation" name="password_confirmation" placeholder="Confirm new password" @if(!$user->email_verified_at) disabled @endif>
                            </div>
                        </div>
                        
                        <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color);">
                            <button type="submit" class="modern-btn" @if(!$user->email_verified_at) disabled @endif>
                                <i class="bx bx-key"></i> Update Credentials
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    // Image Preview Logic
    document.getElementById('photo').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profile-avatar-preview').src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });

    // Toggle Credentials Form Logic
    const btnToggle = document.getElementById('btn-toggle-credentials');
    const container = document.getElementById('credentials-form-container');
    
    if (btnToggle && container) {
        btnToggle.addEventListener('click', function() {
            if (container.style.display === 'none' || container.style.display === '') {
                container.style.display = 'block';
                btnToggle.innerHTML = '<i class="bx bx-x"></i> Cancel';
                btnToggle.classList.remove('btn-outline');
            } else {
                container.style.display = 'none';
                btnToggle.innerHTML = '<i class="bx bx-edit-alt"></i> Edit';
                btnToggle.classList.add('btn-outline');
            }
        });
    }
</script>
@endsection
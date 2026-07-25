@extends('layouts.admin')

@section('title', 'My Profile')

@section('content')
<style>
    :root {
        --pf-bg-card: #ffffff;
        --pf-bg-subtle: #f8fafc;
        --pf-border: #e2e8f0;
        --pf-text-main: #0f172a;
        --pf-text-muted: #64748b;
        --pf-primary: #6366f1;
        --pf-primary-dark: #4f46e5;
        --pf-primary-light: #eef2ff;
        --pf-success: #10b981;
        --pf-warning: #f59e0b;
        --pf-danger: #ef4444;
        --pf-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.04), 0 8px 10px -6px rgba(15, 23, 42, 0.02);
        --pf-radius: 16px;
    }

    .profile-page-wrapper {
        max-width: 1200px;
        margin: 0 auto;
        padding: 1.5rem 1rem 3rem;
        font-family: inherit;
    }

    /* Top Hero Header Banner */
    .profile-hero-card {
        background: var(--pf-bg-card);
        border-radius: var(--pf-radius);
        border: 1px solid var(--pf-border);
        box-shadow: var(--pf-shadow);
        overflow: hidden;
        margin-bottom: 2rem;
        position: relative;
    }

    .profile-hero-banner {
        height: 120px;
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #6366f1 100%);
        position: relative;
    }

    .profile-hero-content {
        padding: 0 2rem 1.5rem;
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 1.5rem;
        margin-top: -50px;
        position: relative;
        z-index: 2;
        flex-wrap: wrap;
    }

    .pf-avatar-wrapper {
        position: relative !important;
        width: 110px !important;
        height: 110px !important;
        min-width: 110px !important;
        max-width: 110px !important;
        min-height: 110px !important;
        max-height: 110px !important;
        flex-shrink: 0 !important;
    }

    .pf-avatar-img {
        width: 110px !important;
        height: 110px !important;
        min-width: 110px !important;
        max-width: 110px !important;
        min-height: 110px !important;
        max-height: 110px !important;
        border-radius: 50% !important;
        object-fit: cover !important;
        border: 4px solid #ffffff !important;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15) !important;
        background: #ffffff !important;
        display: block !important;
    }

    .status-dot-active {
        position: absolute;
        bottom: 4px;
        right: 4px;
        width: 16px;
        height: 16px;
        background-color: var(--pf-success);
        border-radius: 50%;
        border: 2.5px solid #ffffff;
        box-shadow: 0 2px 5px rgba(0,0,0,0.15);
    }

    .user-info-meta {
        flex: 1;
        min-width: 220px;
        margin-bottom: 0.25rem;
    }

    .user-info-meta h2 {
        font-size: 1.4rem;
        font-weight: 800;
        color: var(--pf-text-main);
        margin: 0 0 0.25rem 0;
        line-height: 1.2;
    }

    .user-info-meta p {
        margin: 0 0 0.5rem 0;
        color: var(--pf-text-muted);
        font-size: 0.925rem;
        font-weight: 500;
    }

    .user-status-badges {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .pf-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.35rem 0.85rem;
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 600;
        letter-spacing: 0.01em;
    }

    .pf-badge-verified {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .pf-badge-unverified {
        background: #fef3c7;
        color: #b45309;
        border: 1px solid #fde68a;
    }

    /* Main Grid Layout */
    .profile-main-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 2rem;
    }

    @media (min-width: 992px) {
        .profile-main-grid {
            grid-template-columns: 340px 1fr;
        }
    }

    /* Cards */
    .pf-card {
        background: var(--pf-bg-card);
        border-radius: var(--pf-radius);
        border: 1px solid var(--pf-border);
        box-shadow: var(--pf-shadow);
        overflow: hidden;
        transition: box-shadow 0.2s ease;
    }

    .pf-card-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--pf-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #ffffff;
    }

    .pf-card-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--pf-text-main);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }

    .pf-card-title i {
        font-size: 1.25rem;
        color: var(--pf-primary);
    }

    .pf-card-body {
        padding: 1.5rem;
    }

    /* Form Styles */
    .pf-form-group {
        margin-bottom: 1.25rem;
    }

    .pf-label {
        display: block;
        font-size: 0.825rem;
        font-weight: 600;
        color: var(--pf-text-main);
        margin-bottom: 0.45rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .pf-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .pf-input-icon {
        position: absolute;
        left: 1rem;
        color: var(--pf-text-muted);
        font-size: 1.15rem;
        pointer-events: none;
    }

    .pf-input {
        width: 100%;
        background-color: var(--pf-bg-subtle);
        border: 1.5px solid var(--pf-border);
        color: var(--pf-text-main);
        padding: 0.75rem 1rem;
        border-radius: 10px;
        font-size: 0.925rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .pf-input-has-icon {
        padding-left: 2.75rem;
    }

    .pf-input:focus {
        outline: none;
        border-color: var(--pf-primary);
        background-color: #ffffff;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    }

    .pf-input:disabled, .pf-input[readonly] {
        background-color: #f1f5f9;
        color: var(--pf-text-muted);
        cursor: not-allowed;
        border-color: #e2e8f0;
    }

    /* Custom File Upload Box */
    .custom-file-upload {
        border: 2px dashed var(--pf-border);
        border-radius: 12px;
        padding: 1.25rem 1rem;
        text-align: center;
        background: var(--pf-bg-subtle);
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
    }

    .custom-file-upload:hover {
        border-color: var(--pf-primary);
        background: var(--pf-primary-light);
    }

    .custom-file-upload input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
        width: 100%;
        height: 100%;
    }

    .file-upload-icon {
        width: 42px;
        height: 42px;
        background: #ffffff;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: var(--pf-primary);
        font-size: 1.4rem;
        margin-bottom: 0.5rem;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }

    .file-upload-text {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--pf-text-main);
    }

    .file-upload-subtext {
        font-size: 0.75rem;
        color: var(--pf-text-muted);
        margin-top: 0.25rem;
    }

    /* Buttons */
    .pf-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        color: #ffffff;
        border: none;
        padding: 0.75rem 1.6rem;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 4px 14px rgba(99, 102, 241, 0.25);
    }

    .pf-btn:hover:not(:disabled) {
        opacity: 0.95;
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(99, 102, 241, 0.35);
    }

    .pf-btn:disabled {
        opacity: 0.55;
        cursor: not-allowed;
        box-shadow: none;
    }

    .pf-btn-outline {
        background: #ffffff;
        border: 1.5px solid var(--pf-border);
        color: var(--pf-text-main);
        box-shadow: none;
    }

    .pf-btn-outline:hover:not(:disabled) {
        background: var(--pf-bg-subtle);
        border-color: #cbd5e1;
        transform: none;
    }

    /* Alerts */
    .pf-alert {
        padding: 1rem 1.25rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.02);
    }
    .pf-alert-success { background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; }
    .pf-alert-danger { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }

    /* Security Locked Banner Card */
    .security-locked-banner {
        background: #f8fafc;
        border: 1.5px dashed #cbd5e1;
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
    }

    .security-locked-icon {
        width: 50px;
        height: 50px;
        background: #fee2e2;
        color: #ef4444;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 0.75rem;
    }
</style>

<div class="profile-page-wrapper">

    @if (session('success'))
        <div class="pf-alert pf-alert-success">
            <i class="bx bx-check-circle" style="font-size: 1.3rem;"></i> 
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="pf-alert pf-alert-danger">
            <i class="bx bx-error-circle" style="font-size: 1.3rem; flex-shrink: 0;"></i>
            <ul style="margin: 0; padding-left: 1rem; flex: 1;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Profile Hero Card -->
    <div class="profile-hero-card">
        <div class="profile-hero-banner"></div>
        <div class="profile-hero-content">
            <div class="pf-avatar-wrapper">
                <img src="{{ $user->profile_photo_url }}" id="profile-avatar-preview" class="pf-avatar-img" alt="{{ $user->name }}" style="width: 110px !important; height: 110px !important; min-width: 110px !important; max-width: 110px !important; min-height: 110px !important; max-height: 110px !important; border-radius: 50% !important; object-fit: cover !important; border: 4px solid #ffffff !important; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15) !important; background: #ffffff !important; display: block !important;">
                <div class="status-dot-active" title="Active Account"></div>
            </div>
            
            <div class="user-info-meta">
                <h2>{{ $user->name }}</h2>
                <p>{{ $user->designation ?? 'No Designation' }}</p>
                <div class="user-status-badges">
                    @if($user->email_verified_at)
                        <span class="pf-badge pf-badge-verified"><i class="bx bxs-badge-check"></i> Email Verified</span>
                    @else
                        <span class="pf-badge pf-badge-unverified"><i class="bx bx-error-circle"></i> Unverified Email</span>
                    @endif
                    <span class="pf-badge" style="background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0;">
                        <i class="bx bx-user-check"></i> {{ ucfirst(auth()->user()->role_name ?? 'User') }}
                    </span>
                </div>
            </div>

            @if(!$user->email_verified_at)
                <div style="flex-shrink: 0;">
                    <a href="#verify-identity-section" class="pf-btn" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); box-shadow: 0 4px 14px rgba(245, 158, 11, 0.25);">
                        <i class="bx bx-shield-quarter"></i> Verify Email
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Main Two-Column Layout -->
    <div class="profile-main-grid">
            
        <!-- Left Sidebar Column -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            
            <!-- Quick Contact Info Card -->
            <div class="pf-card">
                <div class="pf-card-header">
                    <h3 class="pf-card-title"><i class="bx bx-id-card"></i> Contact Summary</h3>
                </div>
                <div class="pf-card-body" style="display: flex; flex-direction: column; gap: 1.25rem;">
                    <div style="display: flex; align-items: center; gap: 0.85rem;">
                        <div style="width: 38px; height: 38px; background: #eef2ff; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: var(--pf-primary); font-size: 1.15rem; flex-shrink: 0;">
                            <i class="bx bx-envelope"></i>
                        </div>
                        <div style="overflow: hidden;">
                            <div style="font-size: 0.75rem; color: var(--pf-text-muted); font-weight: 600; text-transform: uppercase;">Email</div>
                            <div style="font-size: 0.875rem; font-weight: 600; color: var(--pf-text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $user->email }}</div>
                        </div>
                    </div>

                    <div style="display: flex; align-items: center; gap: 0.85rem;">
                        <div style="width: 38px; height: 38px; background: #ecfdf5; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: var(--pf-success); font-size: 1.15rem; flex-shrink: 0;">
                            <i class="bx bx-phone"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.75rem; color: var(--pf-text-muted); font-weight: 600; text-transform: uppercase;">Mobile</div>
                            <div style="font-size: 0.875rem; font-weight: 600; color: var(--pf-text-main);">{{ $user->mobile ?? 'Not set' }}</div>
                        </div>
                    </div>

                    <div style="display: flex; align-items: flex-start; gap: 0.85rem;">
                        <div style="width: 38px; height: 38px; background: #fff7ed; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #f97316; font-size: 1.15rem; flex-shrink: 0;">
                            <i class="bx bx-map-pin"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.75rem; color: var(--pf-text-muted); font-weight: 600; text-transform: uppercase;">Address</div>
                            <div style="font-size: 0.85rem; font-weight: 500; color: var(--pf-text-main); line-height: 1.4;">{{ $user->profile->address ?? 'Not specified' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Identity Verification Card (Shown if email unverified) -->
            @if(!$user->email_verified_at)
                <div class="pf-card" id="verify-identity-section" style="border-top: 4px solid var(--pf-warning);">
                    <div class="pf-card-body">
                        <div style="display: flex; align-items: center; gap: 0.6rem; color: var(--pf-warning); margin-bottom: 0.75rem;">
                            <i class="bx bx-mail-send" style="font-size: 1.4rem;"></i>
                            <h3 style="font-size: 1rem; font-weight: 700; margin: 0; color: var(--pf-text-main);">Verify Identity</h3>
                        </div>
                        <p style="color: var(--pf-text-muted); font-size: 0.85rem; line-height: 1.5; margin-bottom: 1.25rem;">
                            Verify your email address to unlock security credentials management and password updates.
                        </p>

                        @if(!session('email_verification_code'))
                            <form action="{{ route('profile.send_code') }}" method="POST">
                                @csrf
                                <button type="submit" class="pf-btn pf-btn-outline" style="width: 100%;">
                                    <i class="bx bx-paper-plane"></i> Send Verification Code
                                </button>
                            </form>
                        @else
                            <form action="{{ route('profile.verify') }}" method="POST">
                                @csrf
                                <div class="pf-form-group">
                                    <label class="pf-label" for="code" style="text-align: center;">Enter 6-Digit Code</label>
                                    <input type="text" class="pf-input" id="code" name="code" placeholder="------" maxlength="6" style="letter-spacing: 0.5em; text-align: center; font-size: 1.25rem; font-weight: 800; background: #ffffff;" required>
                                </div>
                                <button type="submit" class="pf-btn" style="width: 100%; background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                                    <i class="bx bx-check-shield"></i> Confirm Verification
                                </button>
                            </form>
                            
                            <form action="{{ route('profile.send_code') }}" method="POST" style="text-align: center; margin-top: 1rem;">
                                @csrf
                                <button type="submit" style="background: none; border: none; color: var(--pf-primary); font-size: 0.8rem; font-weight: 600; cursor: pointer; text-decoration: underline;">
                                    Resend Verification Code
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Main Column -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            
            <!-- Personal Information Form Card -->
            <div class="pf-card">
                <div class="pf-card-header">
                    <h3 class="pf-card-title"><i class="bx bx-user-circle"></i> Personal Information</h3>
                </div>
                <div class="pf-card-body">
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem;">
                            <div class="pf-form-group">
                                <label class="pf-label" for="name">Full Name</label>
                                <div class="pf-input-wrapper">
                                    <i class="bx bx-user pf-input-icon"></i>
                                    <input type="text" class="pf-input pf-input-has-icon" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                </div>
                            </div>
                            
                            <div class="pf-form-group">
                                <label class="pf-label" for="designation">Designation</label>
                                <div class="pf-input-wrapper">
                                    <i class="bx bx-briefcase pf-input-icon"></i>
                                    <input type="text" class="pf-input pf-input-has-icon" id="designation" name="designation" value="{{ old('designation', $user->designation) }}" placeholder="e.g. Project Manager" @if(!Auth::user()->isSuperAdmin()) readonly @endif>
                                </div>
                            </div>
                        </div>

                        <div class="pf-form-group">
                            <label class="pf-label" for="mobile">Mobile Number</label>
                            <div class="pf-input-wrapper">
                                <i class="bx bx-mobile-alt pf-input-icon"></i>
                                <input type="text" class="pf-input pf-input-has-icon" id="mobile" name="mobile" value="{{ old('mobile', $user->mobile) }}" placeholder="e.g. +91 9999999999">
                            </div>
                        </div>

                        <div class="pf-form-group">
                            <label class="pf-label" for="address">Address</label>
                            <textarea class="pf-input" id="address" name="address" rows="3" style="resize: vertical;" placeholder="Enter complete address...">{{ old('address', $user->profile ? $user->profile->address : '') }}</textarea>
                        </div>
                        
                        <!-- Styled Custom File Upload Zone -->
                        <div class="pf-form-group">
                            <label class="pf-label">Upload Profile Photo</label>
                            <div class="custom-file-upload">
                                <input type="file" id="photo" name="photo" accept="image/*">
                                <div class="file-upload-icon"><i class="bx bx-cloud-upload"></i></div>
                                <div class="file-upload-text" id="file-upload-label-text">Click or drag & drop image to update</div>
                                <div class="file-upload-subtext">Accepted formats: JPG, PNG, WEBP. Max size 5MB.</div>
                            </div>
                        </div>
                        
                        <div style="margin-top: 1.75rem; padding-top: 1.25rem; border-top: 1px solid var(--pf-border); display: flex; justify-content: flex-end;">
                            <button type="submit" class="pf-btn">
                                <i class="bx bx-save"></i> Save Profile Details
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Security & Credentials Card -->
            <div class="pf-card">
                <div class="pf-card-header">
                    <h3 class="pf-card-title"><i class="bx bx-shield-alt-2" style="color: var(--pf-success);"></i> Security & Credentials</h3>
                    @if($user->email_verified_at)
                        <button type="button" id="btn-toggle-credentials" class="pf-btn pf-btn-outline" style="padding: 0.4rem 0.85rem; font-size: 0.8rem;">
                            <i class="bx bx-edit-alt"></i> Edit Credentials
                        </button>
                    @endif
                </div>
                
                <div class="pf-card-body">
                    @if(!$user->email_verified_at)
                        <!-- Clean Inline Notice when Unverified -->
                        <div class="security-locked-banner">
                            <div class="security-locked-icon">
                                <i class="bx bxs-lock"></i>
                            </div>
                            <h4 style="font-size: 1.05rem; font-weight: 700; color: var(--pf-text-main); margin: 0 0 0.35rem 0;">Credentials Editing Locked</h4>
                            <p style="color: var(--pf-text-muted); font-size: 0.875rem; max-width: 440px; margin: 0 auto 1.25rem auto; line-height: 1.5;">
                                To modify your login email address or update password credentials, please verify your email identity first.
                            </p>
                            <a href="#verify-identity-section" class="pf-btn" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); font-size: 0.85rem; padding: 0.6rem 1.2rem;">
                                <i class="bx bx-mail-send"></i> Verify Email Now
                            </a>
                        </div>
                    @else
                        <!-- Credentials Edit Form (Hidden by default until 'Edit' is clicked) -->
                        <div id="credentials-form-container" style="display: {{ ($errors->has('email') || $errors->has('password') || old('email')) ? 'block' : 'none' }};">
                            <form action="{{ route('profile.update_credentials') }}" method="POST">
                                @csrf
                                
                                <div class="pf-form-group">
                                    <label class="pf-label" for="email">Email Address</label>
                                    <div class="pf-input-wrapper">
                                        <i class="bx bx-envelope pf-input-icon"></i>
                                        <input type="email" class="pf-input pf-input-has-icon" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                    </div>
                                </div>
                                
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem;">
                                    <div class="pf-form-group">
                                        <label class="pf-label" for="password">New Password</label>
                                        <div class="pf-input-wrapper">
                                            <i class="bx bx-lock-alt pf-input-icon"></i>
                                            <input type="password" class="pf-input pf-input-has-icon" id="password" name="password" placeholder="Leave blank to keep current">
                                        </div>
                                    </div>
                                    
                                    <div class="pf-form-group">
                                        <label class="pf-label" for="password_confirmation">Confirm Password</label>
                                        <div class="pf-input-wrapper">
                                            <i class="bx bx-lock-check pf-input-icon"></i>
                                            <input type="password" class="pf-input pf-input-has-icon" id="password_confirmation" name="password_confirmation" placeholder="Confirm new password">
                                        </div>
                                    </div>
                                </div>
                                
                                <div style="margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--pf-border); display: flex; justify-content: flex-end;">
                                    <button type="submit" class="pf-btn" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                                        <i class="bx bx-key"></i> Update Credentials
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    // Image Preview & Custom Upload Label Logic
    const photoInput = document.getElementById('photo');
    if (photoInput) {
        photoInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const avatarImg = document.getElementById('profile-avatar-preview');
                    if (avatarImg) avatarImg.src = e.target.result;
                }
                reader.readAsDataURL(file);

                const uploadText = document.getElementById('file-upload-label-text');
                if (uploadText) {
                    uploadText.textContent = 'Selected: ' + file.name;
                    uploadText.style.color = 'var(--pf-primary)';
                }
            }
        });
    }

    // Toggle Credentials Form Logic
    const btnToggle = document.getElementById('btn-toggle-credentials');
    const container = document.getElementById('credentials-form-container');
    
    if (btnToggle && container) {
        btnToggle.addEventListener('click', function() {
            if (container.style.display === 'none' || container.style.display === '') {
                container.style.display = 'block';
                btnToggle.innerHTML = '<i class="bx bx-x"></i> Cancel';
                btnToggle.classList.remove('pf-btn-outline');
            } else {
                container.style.display = 'none';
                btnToggle.innerHTML = '<i class="bx bx-edit-alt"></i> Edit Credentials';
                btnToggle.classList.add('pf-btn-outline');
            }
        });
    }
</script>
@endsection
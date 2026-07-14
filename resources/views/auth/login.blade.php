<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | RCFI Portal</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo_collapsed.png') }}">
    
    <!-- Premium Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;300;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <!-- Premium CSS Layout -->
    <style>
        :root {
            --bg-color: #0b0f19;
            --panel-bg: #111827;
            --panel-border: #1f2937;
            --text-main: #f3f4f6;
            --text-muted: #9ca3af;
            --accent-purple: #6366f1;
            --accent-cyan: #06b6d4;
            --accent-green: #10b981;
            --accent-red: #ef4444;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            font-weight: 300;
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .login-card {
            background-color: var(--panel-bg);
            border: 1px solid var(--panel-border);
            border-radius: 16px;
            width: 100%;
            max-width: 400px;
            padding: 2.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            position: relative;
        }

        /* Avatar styling */
        .avatar-wrapper {
            display: flex;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .avatar-icon {
            width: 64px;
            height: 64px;
            background-color: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: var(--accent-green);
        }

        .login-title {
            text-align: center;
            color: #ffffff;
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .login-subtitle {
            text-align: center;
            color: var(--text-muted);
            font-size: 0.875rem;
            margin-bottom: 2rem;
        }

        /* Form Controls */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-muted);
        }

        .input-group-custom {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-group-custom .input-icon {
            position: absolute;
            left: 1rem;
            color: var(--text-muted);
            font-size: 1.1rem;
            pointer-events: none;
        }

        .form-control-dark {
            background-color: var(--bg-color);
            border: 1px solid var(--panel-border);
            color: #ffffff;
            border-radius: 8px;
            padding: 0.75rem 1rem 0.75rem 2.75rem;
            width: 100%;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .form-control-dark:focus {
            border-color: var(--accent-cyan);
            box-shadow: 0 0 0 1px var(--accent-cyan);
            outline: none;
        }

        .form-control-dark::placeholder {
            color: #4b5563;
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--accent-cyan), var(--accent-purple));
            color: #ffffff;
            border: none;
            font-size: 0.95rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            padding: 0.75rem;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 2rem;
            transition: opacity 0.2s;
        }

        .btn-submit:hover {
            opacity: 0.9;
        }

        .invalid-feedback {
            color: var(--accent-red);
            font-size: 0.8rem;
            margin-top: 0.35rem;
            display: block;
            font-weight: 500;
        }

        .alert-error {
            background-color: rgba(239, 68, 68, 0.1);
            border: 1px solid var(--accent-red);
            color: #ff8080;
            padding: 0.75rem;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }
    </style>
</head>
<body>

    <div class="login-card">
        
        <!-- Header -->
        <div class="avatar-wrapper">
            <div class="avatar-icon">
                <i class='bx bxs-user'></i>
            </div>
        </div>
        <h4 class="login-title">Welcome Back</h4>
        <p class="login-subtitle">Sign in to your dashboard</p>

        <!-- Status Messages -->
        @if (session('status'))
            <div class="alert-success" style="background-color: rgba(16, 185, 129, 0.1); border: 1px solid var(--accent-green); color: #8cf5c6; padding: 0.75rem; border-radius: 8px; font-size: 0.85rem; margin-bottom: 1.5rem; font-weight: 500;">
                {{ session('status') }}
            </div>
        @endif

        <!-- General Role/Authorization Errors -->
        @if ($errors->has('role'))
            <div class="alert-error">
                {{ $errors->first('role') }}
            </div>
        @endif

        @if ($errors->has('email') && !$errors->has('password'))
            <div class="alert-error">
                {{ $errors->first('email') }}
            </div>
        @endif

        <!-- Form -->
        <form action="{{ route('do.admin_login') }}" method="POST">
            @csrf

            <!-- Email Field -->
            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <div class="input-group-custom">
                    <i class="bx bx-envelope input-icon"></i>
                    <input type="email" class="form-control-dark @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="Enter your email" required autofocus>
                </div>
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <!-- Password Field -->
            <div class="form-group">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <label class="form-label" for="password" style="margin-bottom: 0;">Password</label>
                    <a href="{{ route('password.request') }}" style="color: var(--text-muted); font-size: 0.8rem; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='#ffffff'" onmouseout="this.style.color='var(--text-muted)'">Forgot Password?</a>
                </div>
                <div class="input-group-custom">
                    <i class="bx bx-lock-alt input-icon"></i>
                    <input type="password" class="form-control-dark @error('password') is-invalid @enderror" id="password" name="password" placeholder="Enter your password" required>
                </div>
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <!-- Submit -->
            <button class="btn-submit" type="submit">
                Sign In <i class="bx bx-log-in-circle"></i>
            </button>
        </form>

    </div>

</body>
</html>

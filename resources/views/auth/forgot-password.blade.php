<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | RCFI Portal</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo_collapsed.png') }}">
    
    <!-- Premium Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;300;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <style>
        :root {
            --bg-color: #f5f7fb;
            --panel-bg: #ffffff;
            --panel-border: #e2e8f0;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --accent-purple: #6366f1;
            --accent-cyan: #0ea5e9;
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
            font-weight: 400;
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
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.06);
            position: relative;
        }

        .avatar-wrapper {
            display: flex;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .avatar-icon {
            width: 64px;
            height: 64px;
            background-color: #f1f5f9;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: var(--accent-green);
        }

        .login-title {
            text-align: center;
            color: var(--text-main);
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
            color: var(--text-main);
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
            color: #94a3b8;
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--accent-cyan), var(--accent-green));
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
            background-color: rgba(239, 68, 68, 0.05);
            border: 1px solid var(--accent-red);
            color: var(--accent-red);
            padding: 0.75rem;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }

        .alert-success {
            background-color: rgba(16, 185, 129, 0.05);
            border: 1px solid var(--accent-green);
            color: var(--accent-green);
            padding: 0.75rem;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }

        .back-link-wrapper {
            margin-top: 1.5rem;
            text-align: center;
        }

        .back-link {
            color: var(--text-muted);
            font-size: 0.85rem;
            text-decoration: none;
            transition: color 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        .back-link:hover {
            color: var(--text-main);
        }
    </style>
</head>
<body>

    <div class="login-card">
        
        <div class="avatar-wrapper">
            <div class="avatar-icon">
                <i class='bx bx-key'></i>
            </div>
        </div>
        <h4 class="login-title">Forgot Password</h4>
        <p class="login-subtitle">Enter email to receive reset link</p>

        <!-- Status Messages -->
        @if (session('status'))
            <div class="alert-success">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->has('email'))
            <div class="alert-error">
                {{ $errors->first('email') }}
            </div>
        @endif

        <!-- Form -->
        <form action="{{ route('password.email') }}" method="POST">
            @csrf

            <!-- Email Field -->
            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <div class="input-group-custom">
                    <i class="bx bx-envelope input-icon"></i>
                    <input type="email" class="form-control-dark @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="Enter your email" required autofocus>
                </div>
            </div>

            <!-- Submit -->
            <button class="btn-submit" type="submit">
                Send Reset Link <i class="bx bx-send"></i>
            </button>
        </form>

        <div class="back-link-wrapper">
            <a href="{{ route('login') }}" class="back-link">
                <i class="bx bx-left-arrow-alt"></i> Back to Sign In
            </a>
        </div>

    </div>

</body>
</html>

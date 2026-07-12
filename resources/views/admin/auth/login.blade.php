<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - {{ setting('site_name', config('app.name')) }}</title>
    @if(setting('site_favicon'))
        <link rel="icon" href="{{ setting_image('site_favicon') }}">
    @endif
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f7ff;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 16px;
        }
        .login-card {
            background: #fff;
            width: 100%;
            max-width: 380px;
            border-radius: 16px;
            padding: 32px 28px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.08);
        }
        .login-brand { display: flex; flex-direction: column; align-items: center; margin-bottom: 24px; }
        .login-brand img { height: 44px; margin-bottom: 10px; border-radius: 8px; }
        .login-brand h1 { font-size: 18px; font-weight: 700; }
        .login-brand p { font-size: 13px; color: #6b7280; margin-top: 2px; }

        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
        .form-group input {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid #e5e7eb;
            border-radius: 9px;
            font-size: 14px;
            outline: none;
        }
        .form-group input:focus { border-color: #3b5bdb; box-shadow: 0 0 0 3px rgba(59,91,219,0.1); }

        .form-error { color: #dc2626; font-size: 12px; margin-top: 6px; }

        .checkbox-row { display: flex; align-items: center; gap: 8px; margin-bottom: 20px; font-size: 13px; color: #374151; }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: #3b5bdb;
            color: #fff;
            border: none;
            border-radius: 9px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
        }
        .btn-login:hover { background: #2f4ac0; }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            padding: 10px 14px;
            border-radius: 9px;
            font-size: 13px;
            margin-bottom: 16px;
        }

        .back-link { text-align: center; margin-top: 18px; font-size: 13px; }
        .back-link a { color: #6b7280; text-decoration: none; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-brand">
            @if(setting('site_logo'))
                <img src="{{ setting_image('site_logo') }}" alt="{{ setting('site_name') }}">
            @endif
            <h1>{{ setting('site_name', config('app.name')) }}</h1>
            <p>Admin Panel Login</p>
        </div>

        @if($errors->any())
            <div class="alert-error">{{ $errors->first() }}</div>
        @endif
        @if(session('error'))
            <div class="alert-error">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="checkbox-row">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Remember me</label>
            </div>

            <button type="submit" class="btn-login">Sign In</button>
        </form>

        <div class="back-link">
            <a href="{{ route('home') }}">&larr; Back to store</a>
        </div>
    </div>
</body>
</html>
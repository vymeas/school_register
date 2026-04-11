<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login — School Register</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="login-wrapper">
    <div class="login-card">
        <div class="login-logo">
            <div class="logo-icon">SR</div>
            <h2>School Register</h2>
            <p>Sign in to your admin account</p>
        </div>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Enter username" value="{{ old('username') }}" required autofocus>
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter password" required>
            </div>
            <button type="submit" class="btn btn-primary login-btn">Sign In</button>
        </form>

        <p style="text-align:center; margin-top:20px; font-size:12px; color:var(--text-muted);">
            Default: <strong>superadmin</strong> / <strong>password</strong>
        </p>
    </div>
</div>
</body>
</html>

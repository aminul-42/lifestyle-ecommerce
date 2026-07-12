@extends('layouts.storefront')

@section('title', 'Login')

@push('styles')
<style>
    .login-section{
        min-height:70vh;
        display:flex;
        align-items:center;
        justify-content:center;
        padding:60px 20px;
    }

    .login-box{
        width:100%;
        max-width:430px;
        background:var(--card);
        border:1px solid var(--stone);
        border-radius:22px;
        padding:40px;
        text-align:center;
        box-shadow:0 18px 45px rgba(20,20,20,.08);
    }

    .login-logo{
        width:72px;
        height:72px;
        object-fit:contain;
        margin-bottom:18px;
    }

    .login-title{
        font-size:30px;
        font-weight:800;
        color:var(--ink);
        margin-bottom:8px;
    }

    .login-text{
        color:var(--mute);
        margin-bottom:32px;
        line-height:1.7;
    }

    .error-msg{
        background:#fee2e2;
        color:#991b1b;
        border-radius:12px;
        padding:14px;
        margin-bottom:20px;
        font-size:14px;
    }

    .btn-google{
        width:100%;
        display:flex;
        align-items:center;
        justify-content:center;
        gap:14px;

        padding:15px 18px;

        border-radius:14px;

        border:1px solid var(--stone);

        background:#fff;

        color:var(--ink);

        font-weight:700;

        transition:.25s;
    }

    .btn-google:hover{
        transform:translateY(-2px);
        box-shadow:0 10px 25px rgba(20,20,20,.08);
        border-color:var(--accent);
    }

    .btn-google svg{
        width:22px;
        height:22px;
    }

    .login-note{
        margin-top:22px;
        font-size:13px;
        color:var(--mute);
    }
</style>
@endpush

@section('content')

<section class="login-section">

    <div class="login-box">

        @if(setting('site_logo'))
            <img
                src="{{ setting_image('site_logo') }}"
                class="login-logo"
                alt="{{ setting('site_name') }}">
        @endif

        <h1 class="login-title">
            Welcome Back
        </h1>

        <p class="login-text">
            Sign in with your Google account to continue shopping and manage your orders.
        </p>

        @if(session('error'))
            <div class="error-msg">
                {{ session('error') }}
            </div>
        @endif

        <a href="{{ route('auth.google') }}" class="btn-google">

            <svg viewBox="0 0 48 48">
                <path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3C33.9 32.6 29.4 36 24 36c-6.6 0-12-5.4-12-12s5.4-12 12-12c3.1 0 5.9 1.1 8.1 3.1l5.7-5.7C34.5 6.1 29.5 4 24 4 12.9 4 4 12.9 4 24s8.9 20 20 20 20-8.9 20-20c0-1.3-.1-2.7-.4-3.5z"/>
                <path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.7 15.9 19 13 24 13c3.1 0 5.9 1.1 8.1 3.1l5.7-5.7C34.5 6.1 29.5 4 24 4c-7.7 0-14.3 4.3-17.7 10.7z"/>
                <path fill="#4CAF50" d="M24 44c5.3 0 10.2-2 13.9-5.4l-6.4-5.4C29.4 34.9 26.8 36 24 36c-5.3 0-9.8-3.4-11.4-8.1l-6.6 5.1C9.6 39.6 16.2 44 24 44z"/>
                <path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-.8 2.3-2.3 4.3-4.2 5.7l6.4 5.4C39.9 36.9 44 31 44 24c0-1.3-.1-2.7-.4-3.5z"/>
            </svg>

            Continue with Google

        </a>

        <p class="login-note">
            Fast, secure and password-free sign in.
        </p>

    </div>

</section>

@endsection
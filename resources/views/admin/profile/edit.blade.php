@extends('layouts.app')

@section('title', 'My Profile')
@section('page-title', 'My Profile')
@section('page-subtitle', 'Manage your account details and password')

@section('content')

    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; max-width:900px;">

        {{-- Profile Info --}}
        <div class="table-wrap" style="padding:24px;">
            <h3 style="font-size:15px; font-weight:700; margin-bottom:18px;">Profile Information</h3>

            <form method="POST" action="{{ route('admin.profile.info') }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required>
                    @error('name', 'updateInfo') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required>
                    @error('email', 'updateInfo') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-primary btn-block">Save Changes</button>
            </form>
        </div>

        {{-- Change Password --}}
        <div class="table-wrap" style="padding:24px;">
            <h3 style="font-size:15px; font-weight:700; margin-bottom:18px;">Change Password</h3>

            <form method="POST" action="{{ route('admin.profile.password') }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label>Current Password</label>
                    <input type="password" name="current_password" required>
                    @error('current_password') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="password" required>
                    @error('password') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="password_confirmation" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Update Password</button>
            </form>
        </div>

    </div>

@endsection
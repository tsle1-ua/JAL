@extends('layouts.app')

@section('content')
<div class="container py-5" style="max-width: 600px;">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">{{ __('Register') }}</div>
        <div class="card-body">
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">{{ __('Name') }}</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus class="form-control" autocomplete="name">
                    @error('name')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">{{ __('Email Address') }}</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required class="form-control" autocomplete="email">
                    @error('email')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="role" class="form-label">{{ __('Register As') }}</label>
                    <select id="role" name="role" required class="form-select">
                        <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Estudiante</option>
                        <option value="owner" {{ old('role') == 'owner' ? 'selected' : '' }}>Propietario</option>
                    </select>
                    @error('role')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">{{ __('Password') }}</label>
                    <input id="password" type="password" name="password" required class="form-control" autocomplete="new-password">
                    @error('password')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="password-confirm" class="form-label">{{ __('Confirm Password') }}</label>
                    <input id="password-confirm" type="password" name="password_confirmation" required class="form-control" autocomplete="new-password">
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">{{ __('Register') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

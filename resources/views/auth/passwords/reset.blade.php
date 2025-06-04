@extends('layouts.app')

@section('content')
<div class="container py-5" style="max-width: 500px;">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">{{ __('Reset Password') }}</div>
        <div class="card-body">
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <div class="mb-3">
                    <label for="email" class="form-label">{{ __('Email Address') }}</label>
                    <input id="email" type="email" name="email" value="{{ $email ?? old('email') }}" required autofocus class="form-control @error('email') is-invalid @enderror" autocomplete="email">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">{{ __('Password') }}</label>
                    <input id="password" type="password" name="password" required class="form-control @error('password') is-invalid @enderror" autocomplete="new-password">
                    <div class="progress mt-2" style="height: 5px;">
                        <div id="strength-bar" class="progress-bar" role="progressbar"></div>
                    </div>
                    <small id="strength-text" class="form-text"></small>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="password-confirm" class="form-label">{{ __('Confirm Password') }}</label>
                    <input id="password-confirm" type="password" name="password_confirmation" required class="form-control" autocomplete="new-password">
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">{{ __('Reset Password') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('password');
        const bar = document.getElementById('strength-bar');
        const text = document.getElementById('strength-text');
        if (!input) return;
        input.addEventListener('input', () => {
            const val = input.value;
            let strength = 0;
            if (val.length >= 8) strength++;
            if (/[A-Z]/.test(val)) strength++;
            if (/[0-9]/.test(val)) strength++;
            if (/[^A-Za-z0-9]/.test(val)) strength++;
            const percent = (strength / 4) * 100;
            bar.style.width = percent + '%';
            bar.classList.remove('bg-danger','bg-warning','bg-success');
            if (strength <= 1) bar.classList.add('bg-danger');
            else if (strength === 2) bar.classList.add('bg-warning');
            else bar.classList.add('bg-success');
            const messages = ['Very weak','Weak','Good','Strong'];
            text.textContent = val ? messages[strength] : '';
        });
    });
</script>
@endsection

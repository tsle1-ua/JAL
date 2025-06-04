@extends('layouts.app')

@section('content')
<div class="container py-5" style="max-width: 500px;">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">{{ __('Two Factor Challenge') }}</div>
        <div class="card-body">
            <form method="POST" action="{{ route('two-factor.login.store') }}">
                @csrf
                <div class="mb-3">
                    <label for="code" class="form-label">{{ __('Authentication Code') }}</label>
                    <input id="code" type="text" name="code" inputmode="numeric" autocomplete="one-time-code" class="form-control" required autofocus>
                    @error('code')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="recovery_code" class="form-label">{{ __('Recovery Code') }}</label>
                    <input id="recovery_code" type="text" name="recovery_code" class="form-control" autocomplete="one-time-code">
                    @error('recovery_code')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">{{ __('Verify') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

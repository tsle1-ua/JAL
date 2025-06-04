@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">{{ __('Notification Settings') }}</h1>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('settings.notifications.update') }}">
        @csrf
        <div class="form-check form-switch mb-3">
            <input type="hidden" name="notifications_enabled" value="0">
            <input class="form-check-input" type="checkbox" role="switch" id="notifications_enabled" name="notifications_enabled" value="1" {{ auth()->user()->notifications_enabled ? 'checked' : '' }}>
            <label class="form-check-label" for="notifications_enabled">{{ __('Enable web push notifications') }}</label>
        </div>
        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
    </form>
</div>
@endsection

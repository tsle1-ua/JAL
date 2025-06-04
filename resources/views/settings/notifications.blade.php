@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Configuración de notificaciones</h1>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('settings.notifications.update') }}">
        @csrf
        <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" id="email_notifications" name="email_notifications" value="1" {{ $settings->email_notifications ? 'checked' : '' }}>
            <label class="form-check-label" for="email_notifications">Recibir notificaciones por email</label>
        </div>
        <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" id="push_notifications" name="push_notifications" value="1" {{ $settings->push_notifications ? 'checked' : '' }}>
            <label class="form-check-label" for="push_notifications">Recibir notificaciones push</label>
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
</div>
@endsection

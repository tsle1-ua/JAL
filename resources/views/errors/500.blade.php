@extends('layouts.app')

@section('content')
<div class="container py-5 text-center">
    <i class="bi bi-exclamation-triangle-fill display-1 text-warning"></i>
    <h1 class="display-4 mt-4">Ups! Algo salió mal</h1>
    <p class="lead">Nuestro equipo está trabajando para solucionarlo.</p>
    <a href="{{ url('/') }}" class="btn btn-primary mt-3">Volver al inicio</a>
</div>
@endsection

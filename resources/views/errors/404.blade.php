@extends('layouts.app')

@section('content')
<div class="container text-center py-5">
    <h1 class="display-4">Página no encontrada</h1>
    <p class="lead">La página que buscas no existe.</p>
    <a href="{{ route('home') }}" class="btn btn-primary">Volver al inicio</a>
</div>
@endsection

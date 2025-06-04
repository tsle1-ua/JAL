@extends('layouts.app')

@section('content')
<div class="container text-center py-5">
    <h1 class="mb-4">RoomieMatch</h1>
    <p class="lead">Encuentra a tu compañero de piso ideal. Regístrate para empezar a conectar con otros estudiantes.</p>
    <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Regístrate</a>
</div>
@endsection

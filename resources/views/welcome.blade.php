@extends('layouts.app')

@section('content')
<div class="bg-primary text-white py-5">
    <div class="container text-center">
        <h1 class="display-4 mb-2">Bienvenido a Spandam</h1>
        <p class="lead">Tu plataforma integral para universitarios</p>
        <a href="{{ route('register') }}" class="btn btn-light mt-3">Regístrate</a>
    </div>
</div>
<div class="container py-5">
    <div class="row text-center g-4">
        <div class="col-md-4">
            <h3>Alojamientos</h3>
            <p>Encuentra y publica pisos de forma sencilla y segura.</p>
        </div>
        <div class="col-md-4">
            <h3>RoomieMatch</h3>
            <p>Conecta con compañeros de piso compatibles contigo.</p>
        </div>
        <div class="col-md-4">
            <h3>Eventos</h3>
            <p>Descubre y organiza eventos universitarios cerca de ti.</p>
        </div>
    </div>
</div>
@endsection

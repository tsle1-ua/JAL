@extends('layouts.app')

@section('content')
<div class="bg-blue-600 text-white py-12">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <h1 class="text-4xl font-bold mb-2">Bienvenido a Spandam</h1>
        <p class="text-xl">Tu plataforma integral para universitarios</p>
        <a href="{{ route('register') }}" class="mt-4 inline-block bg-white text-blue-600 px-6 py-3 rounded shadow">Regístrate</a>
    </div>
</div>
<div class="max-w-7xl mx-auto grid md:grid-cols-3 gap-8 py-12 px-4 text-center">
    <div>
        <h3 class="text-lg font-semibold mb-2">Alojamientos</h3>
        <p>Encuentra y publica pisos de forma sencilla y segura.</p>
    </div>
    <div>
        <h3 class="text-lg font-semibold mb-2">RoomieMatch</h3>
        <p>Conecta con compañeros de piso compatibles contigo.</p>
    </div>
    <div>
        <h3 class="text-lg font-semibold mb-2">Eventos</h3>
        <p>Descubre y organiza eventos universitarios cerca de ti.</p>
    </div>
</div>
@endsection

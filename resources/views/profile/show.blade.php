@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Mi Perfil</h1>
    <a href="{{ route('profile.edit') }}" class="btn btn-primary mb-3">Editar Perfil</a>
    <div class="card">
        <div class="card-body">
            @if($profile->profile_image_url)
                <img src="{{ $profile->profile_image_url }}" alt="Imagen de perfil" class="img-thumbnail mb-3" style="max-width: 200px;">
            @endif
            <p><strong>Biografía:</strong> {{ $profile->bio }}</p>
            <p><strong>Género:</strong> {{ $profile->gender }}</p>
            <p><strong>Edad:</strong> {{ $profile->age }}</p>
            <p><strong>Hobbies:</strong> {{ $profile->hobbies_string }}</p>
            <p><strong>Año académico:</strong> {{ $profile->academic_year }}</p>
            <p><strong>Carrera:</strong> {{ $profile->major }}</p>
            <p><strong>Universidad:</strong> {{ $profile->university_name }}</p>
            <p><strong>Buscando compañero de piso:</strong> {{ $profile->looking_for_roommate ? 'Sí' : 'No' }}</p>
        </div>
    </div>
</div>
@endsection

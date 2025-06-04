@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Mi Perfil</h1>
    @if($profile && $profile->profile_image_url)
        <img src="{{ $profile->profile_image_url }}" alt="Profile" class="img-thumbnail mb-3" width="150">
    @endif
    <p>{{ $profile->bio }}</p>
    <a href="{{ route('profile.edit') }}" class="btn btn-primary">Editar perfil</a>
</div>
@endsection

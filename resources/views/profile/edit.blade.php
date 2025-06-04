@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Perfil</h1>
    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        <div class="mb-3">
            <label for="bio" class="form-label">Bio</label>
            <textarea name="bio" id="bio" class="form-control">{{ old('bio', $profile->bio) }}</textarea>
        </div>
        <div class="mb-3">
            <label for="profile_image" class="form-label">Imagen de perfil</label>
            <input type="file" name="profile_image" id="profile_image" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Editar Perfil</h1>
    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        <div class="mb-3">
            <label class="form-label">Biografía</label>
            <textarea name="bio" class="form-control">{{ old('bio', $profile->bio) }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Género</label>
            <select name="gender" class="form-select">
                <option value="">Selecciona una opción</option>
                <option value="masculino" {{ old('gender', $profile->gender) == 'masculino' ? 'selected' : '' }}>Masculino</option>
                <option value="femenino" {{ old('gender', $profile->gender) == 'femenino' ? 'selected' : '' }}>Femenino</option>
                <option value="no-binario" {{ old('gender', $profile->gender) == 'no-binario' ? 'selected' : '' }}>No binario</option>
                <option value="prefiero-no-decir" {{ old('gender', $profile->gender) == 'prefiero-no-decir' ? 'selected' : '' }}>Prefiero no decir</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Edad</label>
            <input type="number" name="age" class="form-control" value="{{ old('age', $profile->age) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Preferencia de fumar</label>
            <select name="smoking_preference" class="form-select">
                <option value="">Selecciona una opción</option>
                <option value="fumador" {{ old('smoking_preference', $profile->smoking_preference) == 'fumador' ? 'selected' : '' }}>Fumador</option>
                <option value="no-fumador" {{ old('smoking_preference', $profile->smoking_preference) == 'no-fumador' ? 'selected' : '' }}>No fumador</option>
                <option value="flexible" {{ old('smoking_preference', $profile->smoking_preference) == 'flexible' ? 'selected' : '' }}>Flexible</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Preferencia de mascotas</label>
            <select name="pet_preference" class="form-select">
                <option value="">Selecciona una opción</option>
                <option value="tiene-mascotas" {{ old('pet_preference', $profile->pet_preference) == 'tiene-mascotas' ? 'selected' : '' }}>Tiene mascotas</option>
                <option value="le-gustan-mascotas" {{ old('pet_preference', $profile->pet_preference) == 'le-gustan-mascotas' ? 'selected' : '' }}>Le gustan las mascotas</option>
                <option value="no-mascotas" {{ old('pet_preference', $profile->pet_preference) == 'no-mascotas' ? 'selected' : '' }}>No mascotas</option>
                <option value="flexible" {{ old('pet_preference', $profile->pet_preference) == 'flexible' ? 'selected' : '' }}>Flexible</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Nivel de limpieza (1-5)</label>
            <input type="number" name="cleanliness_level" min="1" max="5" class="form-control" value="{{ old('cleanliness_level', $profile->cleanliness_level) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Horario de sueño</label>
            <select name="sleep_schedule" class="form-select">
                <option value="">Selecciona una opción</option>
                <option value="madrugador" {{ old('sleep_schedule', $profile->sleep_schedule) == 'madrugador' ? 'selected' : '' }}>Madrugador</option>
                <option value="noctambulo" {{ old('sleep_schedule', $profile->sleep_schedule) == 'noctambulo' ? 'selected' : '' }}>Noctámbulo</option>
                <option value="flexible" {{ old('sleep_schedule', $profile->sleep_schedule) == 'flexible' ? 'selected' : '' }}>Flexible</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Hobbies (separados por comas)</label>
            <input type="text" name="hobbies" class="form-control" value="{{ old('hobbies', $profile->hobbies_string) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Año académico</label>
            <input type="text" name="academic_year" class="form-control" value="{{ old('academic_year', $profile->academic_year) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Carrera</label>
            <input type="text" name="major" class="form-control" value="{{ old('major', $profile->major) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Universidad</label>
            <input type="text" name="university_name" class="form-control" value="{{ old('university_name', $profile->university_name) }}">
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" name="looking_for_roommate" id="looking_for_roommate" class="form-check-input" value="1" {{ old('looking_for_roommate', $profile->looking_for_roommate) ? 'checked' : '' }}>
            <label class="form-check-label" for="looking_for_roommate">Busco compañero de piso</label>
        </div>
        <div class="mb-3">
            <label class="form-label">Imagen de perfil</label>
            <input type="file" name="profile_image" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
</div>
@endsection

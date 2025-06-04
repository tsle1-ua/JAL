@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Añadir zona de ocio</h1>
    <form method="POST" action="{{ route('leisure-zones.store') }}" enctype="multipart/form-data" role="form">
        @csrf
        <div class="mb-3">
            <label class="form-label" for="zone-name">Nombre</label>
            <input id="zone-name" type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label" for="zone-description">Descripción</label>
            <textarea id="zone-description" name="description" class="form-control"></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label" for="zone-city">Ciudad</label>
            <input id="zone-city" type="text" name="city" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label" for="zone-university">Universidad</label>
            <input id="zone-university" type="text" name="university" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label" for="zone-image">Imagen</label>
            <input id="zone-image" type="file" name="image" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
</div>
@endsection

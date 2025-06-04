@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Añadir zona de ocio</h1>
    <form method="POST" action="{{ route('leisure-zones.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea name="description" class="form-control"></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Ciudad</label>
            <input type="text" name="city" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Universidad</label>
            <input type="text" name="university" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Imagen</label>
            <input type="file" name="image" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
</div>
@endsection

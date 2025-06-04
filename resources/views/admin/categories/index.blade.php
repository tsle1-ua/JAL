@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Categorías</h1>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary mb-3">Crear categoría</a>

    <table class="table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        @foreach($categories as $category)
            <tr>
                <td>{{ $category->name }}</td>
                <td>
                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-secondary">Editar</a>
                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar?')">Eliminar</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection

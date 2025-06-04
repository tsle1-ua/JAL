@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Calculadora de notas de corte</h1>
    <form method="GET" class="row g-2 mb-3" role="search">
        <div class="col-md-3">
            <label for="calc-grade" class="visually-hidden">Tu nota</label>
            <input id="calc-grade" type="number" step="0.01" name="grade" value="{{ request('grade') }}" class="form-control" placeholder="Tu nota">
        </div>
        <div class="col-md-3">
            <label for="calc-city" class="visually-hidden">Ciudad opcional</label>
            <input id="calc-city" type="text" name="city" value="{{ request('city') }}" class="form-control" placeholder="Ciudad opcional">
        </div>
        <div class="col-md-3">
            <button class="btn btn-success w-100">Buscar</button>
        </div>
    </form>
    @isset($results)
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Grado</th>
                <th>Universidad</th>
                <th>Ciudad</th>
                <th>Nota de corte</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($results as $info)
            <tr>
                <td>{{ $info->degree_name }}</td>
                <td>{{ $info->university_name }}</td>
                <td>{{ $info->city }}</td>
                <td>{{ $info->cut_off_mark }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center">No hay resultados</td></tr>
            @endforelse
        </tbody>
    </table>
    @endisset
</div>
@endsection

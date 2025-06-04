@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Notas de corte</h1>
    <form method="GET" class="row g-2 mb-3" role="search">
        <div class="col-md-3">
            <label for="cutoff-city" class="visually-hidden">Ciudad</label>
            <input id="cutoff-city" type="text" name="city" value="{{ request('city') }}" class="form-control" placeholder="Ciudad">
        </div>
        <div class="col-md-3">
            <label for="cutoff-min" class="visually-hidden">Nota mínima</label>
            <input id="cutoff-min" type="number" step="0.01" name="min_mark" value="{{ request('min_mark') }}" class="form-control" placeholder="Nota mínima">
        </div>
        <div class="col-md-3">
            <label for="cutoff-max" class="visually-hidden">Nota máxima</label>
            <input id="cutoff-max" type="number" step="0.01" name="max_mark" value="{{ request('max_mark') }}" class="form-control" placeholder="Nota máxima">
        </div>
        <div class="col-md-3">
            <button class="btn btn-primary w-100">Filtrar</button>
        </div>
    </form>
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
            @foreach ($marks as $info)
            <tr>
                <td>{{ $info->degree_name }}</td>
                <td>{{ $info->university_name }}</td>
                <td>{{ $info->city }}</td>
                <td>{{ $info->cut_off_mark }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $marks->withQueryString()->links() }}
</div>
@endsection

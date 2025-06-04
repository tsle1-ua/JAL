@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Activar pago automático</h1>
    <p>Suscribirse al anuncio: <strong>{{ $listing->title }}</strong></p>

    <form method="POST" action="{{ route('subscriptions.store', $listing) }}" role="form">
        @csrf
        <div class="mb-3">
            <label class="form-label" for="monthly_amount">Importe mensual</label>
            <input type="number" step="0.01" class="form-control" id="monthly_amount" name="monthly_amount" required>
        </div>
        <div class="mb-3">
            <label class="form-label" for="payment_method">Método de pago</label>
            <input type="text" class="form-control" id="payment_method" name="payment_method" required>
        </div>
        <button type="submit" class="btn btn-primary">Confirmar</button>
    </form>
</div>
@endsection

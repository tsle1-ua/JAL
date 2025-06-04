@extends('layouts.app')

@section('content')
<div class="bg-light py-5">
    <div class="container">
        <h1 class="mb-4">{{ __('Dashboard') }}</h1>
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif
        <p class="lead">{{ __('You are logged in!') }}</p>
        <a href="{{ route('listings.my') }}" class="btn btn-primary mt-3">Mis anuncios</a>
    </div>
</div>
@endsection

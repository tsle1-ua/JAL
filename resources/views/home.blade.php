@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">{{ __('Dashboard') }}</h1>
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
</div>
@endsection

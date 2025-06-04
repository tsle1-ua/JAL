@extends('layouts.app')

@section('content')
<div class="bg-gray-100 py-10">
    <div class="max-w-7xl mx-auto px-4">
        <h1 class="text-2xl font-semibold mb-4">{{ __('Dashboard') }}</h1>
        @if (session('status'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                {{ session('status') }}
            </div>
        @endif

    </div>
</div>
@endsection

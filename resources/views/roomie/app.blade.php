@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="alert alert-success d-none" id="matchNotification"></div>

    <h2 class="mb-3">Usuarios sugeridos</h2>
    <div class="row">
        @forelse($candidates as $candidate)
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">{{ $candidate->name }}</h5>
                        <form method="POST" action="{{ route('roomie.like', $candidate->id) }}" class="like-form d-inline">
                            @csrf
                            <button class="btn btn-sm btn-success me-2">Like</button>
                        </form>
                        <form method="POST" action="{{ route('roomie.dislike', $candidate->id) }}" class="dislike-form d-inline">
                            @csrf
                            <button class="btn btn-sm btn-danger">Dislike</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <p>No hay usuarios para mostrar.</p>
        @endforelse
    </div>
</div>
@endsection


@extends('layouts.app')

@section('content')
<div class="container py-4">
    @if(session('new_match'))
        <div class="alert alert-success" id="matchAlert">
            ¡Enhorabuena! Has hecho match con {{ session('new_match') }}.
        </div>
    @endif

    <h2 class="mb-3">Usuarios sugeridos</h2>
    <div class="row">
        @forelse($candidates as $candidate)
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">{{ $candidate->name }}</h5>
                        <form method="POST" action="{{ route('roomie.like', $candidate->id) }}" role="form">
                            @csrf
                            <button class="btn btn-sm btn-success">Like</button>
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const alert = document.getElementById('matchAlert');
        if(alert) {
            setTimeout(() => alert.remove(), 4000);
        }
    });
</script>
@endpush

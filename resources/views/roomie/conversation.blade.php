@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2>Conversación</h2>
    <div class="border rounded p-3 mb-3" style="height:300px; overflow-y:scroll;">
        @foreach($messages as $message)
            <div class="mb-2">
                <strong>{{ $message->sender->name }}:</strong>
                {{ $message->content }}
            </div>
        @endforeach
    </div>
    <form method="POST" action="{{ route('roomie.message', $match->id) }}" role="form">
        @csrf
        <div class="input-group">
            <label for="message-content" class="visually-hidden">Mensaje</label>
            <input id="message-content" type="text" name="content" class="form-control" placeholder="Escribe un mensaje">
            <button class="btn btn-primary">Enviar</button>
        </div>
    </form>
</div>
@endsection

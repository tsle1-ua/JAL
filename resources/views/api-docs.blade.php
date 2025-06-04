@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist/swagger-ui.css">
<div class="container py-4">
    <h1 class="mb-4">API Documentation</h1>
    <div id="swagger-ui"></div>
</div>
<script src="https://unpkg.com/swagger-ui-dist/swagger-ui-bundle.js"></script>
<script>
    window.onload = () => {
        SwaggerUIBundle({
            url: "{{ url('/docs/api.yaml') }}",
            dom_id: '#swagger-ui'
        });
    };
</script>
@endsection

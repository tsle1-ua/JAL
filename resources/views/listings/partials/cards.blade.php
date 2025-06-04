@foreach($listings as $listing)
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            @if($listing->first_image_url)
                <img src="{{ $listing->first_image_url }}" class="card-img-top" alt="Imagen del piso">
            @endif
            <div class="card-body">
                <h5 class="card-title">{{ $listing->title }}</h5>
                <p class="card-text">{{ $listing->city }} - {{ $listing->formatted_price }}</p>
                <p class="card-text">
                    {!! $listing->occupant_icons !!}
                </p>
                <a href="{{ route('listings.show', $listing) }}" class="btn btn-sm btn-outline-primary">Ver más</a>
            </div>
        </div>
    </div>
@endforeach

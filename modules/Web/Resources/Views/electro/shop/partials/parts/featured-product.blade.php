<div class="featured-product mb-4">
    <h4 class="mb-3">Related Products</h4>

    @foreach($relatedProducts as $related)
        <div class="featured-product-item d-flex align-items-center mb-3">
            <div class="rounded me-4" style="width: 100px; height: 100px; flex-shrink: 0;">
                <img src="{{ asset('uploads/' . $related->location. '/' .$related->image_name) }}" class="img-fluid rounded" alt="{{ $related->name }}" style="object-fit: cover; width: 100%; height: 100%;">
            </div>
            <div>
                <a href="{{ route('web::web.shop.show', $related->id) }}" class="text-dark">
                    <h6 class="mb-2">{{ $related->name }}</h6>
                </a>
                <div class="d-flex mb-2" style="font-size: 12px;">
                    @php $rating = $related->average_rating; @endphp
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fa fa-star {{ $i <= $rating ? 'text-secondary' : 'text-muted' }}"></i>
                    @endfor
                </div>
                <div class="d-flex mb-2">
                    <h5 class="fw-bold me-2">Rp. {{ number_format($related->price, 2) }} </h5>
                    @if($related->old_price)
                        <h5 class="text-danger text-decoration-line-through">Rp. {{ number_format($related->old_price, 2) }}</h5>
                    @endif
                </div>
            </div>
        </div>
    @endforeach

    @if($relatedProducts->isEmpty())
        <p class="text-muted small">No related products found.</p>
    @endif

    <div class="d-flex justify-content-center my-4">
        <a href="#" class="btn btn-primary px-4 py-3 rounded-pill w-100">View More</a>
    </div>
</div>

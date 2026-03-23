<div class="container-fluid related-product">
    <div class="container">
        <div class="mx-auto text-center pb-5" style="max-width: 700px;">
            <h4 class="text-primary mb-4 border-bottom border-primary border-2 d-inline-block p-2 title-border-radius wow fadeInUp"
                data-wow-delay="0.1s">Related Products</h4>
            <p class="wow fadeInUp" data-wow-delay="0.2s">Cek produk serupa yang mungkin Anda sukai dari kategori yang sama.</p>
        </div>

        <div class="related-carousel owl-carousel pt-4">
            @foreach($relatedProducts as $related)
                <div class="related-item rounded">
                    <div class="related-item-inner border rounded">
                        <div class="related-item-inner-item">
                            <img src="{{ asset('uploads/' . $related->location. '/' . $related->image_name) }}" class="img-fluid w-100 rounded-top"
                                 alt="{{ $related->name }}" style="height: 250px; object-fit: cover;">

                            @if($related->created_at->diffInDays() < 7)
                                <div class="related-new">New</div>
                            @endif

                            <div class="related-details">
                                <a href="{{ route('web::web.shop.show', $related->id) }}"><i class="fa fa-eye fa-1x"></i></a>
                            </div>
                        </div>
                        <div class="text-center rounded-bottom p-4">
                            <a href="#" class="d-block mb-2">{{ $related->category->name ?? 'Uncategorized' }}</a>
                            <a href="{{ route('web::web.shop.show', $related->id) }}" class="d-block h4">
                                {{ Str::limit($related->name, 25) }}
                            </a>

                            @if($related->old_price)
                                <del class="me-2 fs-5">Rp. {{ number_format($related->old_price, 2) }}</del>
                            @endif
                            <span class="text-primary fs-5">Rp. {{ number_format($related->price, 2) }}</span>
                        </div>
                    </div>
                    <div class="related-item-add border border-top-0 rounded-bottom text-center p-4 pt-0">
                        @include('web::components.chart-version.electro.add-to-chart', ['productId' => $related->id])

                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex">
                                @php $avgRating = $related->average_rating; @endphp
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $avgRating ? 'text-primary' : '' }}"></i>
                                @endfor
                            </div>
                            <div class="d-flex">
                                @include('web::components.chart-version.electro.whistlist',
                                [
                                    'productId' => $related->id,
                                    'isWishlisted' => $related->is_wishlisted ?? false
                                ])
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="tab-pane" id="nav-mission" role="tabpanel" aria-labelledby="nav-mission-tab">
    <div id="review-list-container"
         data-product-id="{{ $product->id }}"
         style="max-height: 500px; overflow-y: auto; overflow-x: hidden; padding-right: 10px;">

        @forelse($product->reviews()->latest()->get() as $review)
            <div class="d-flex mb-4 border-bottom pb-3 review-item">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm"
                    style="width: 80px; height: 80px; min-width: 80px; background-color: {{ '#' . substr(md5($review->name), 0, 6) }}; font-size: 24px;">
                    {{ strtoupper(substr($review->name, 0, 1)) }}
                </div>

                <div class="w-100 ms-4">
                    <p class="mb-2" style="font-size: 14px;">{{ $review->created_at->format('M d, Y') }}</p>
                    <div class="d-flex justify-content-between">
                        <h5>{{ $review->name }}</h5>
                        <div class="d-flex mb-3" style="font-size: 12px;">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fa fa-star {{ $i <= $review->rating ? 'text-secondary' : 'text-muted' }}"></i>
                            @endfor
                        </div>
                    </div>
                    <p class="text-dark">{{ $review->description }}</p>
                </div>
            </div>
        @empty
            <div class="text-center py-5 no-review-placeholder">
                <i class="fa fa-comments-o fa-3x text-muted mb-3 d-block"></i>
                <h5 class="text-muted fw-normal">Belum ada review untuk produk ini.</h5>
                <p class="small text-secondary">Jadilah yang pertama memberikan ulasan!</p>
            </div>
        @endforelse
    </div>
</div>

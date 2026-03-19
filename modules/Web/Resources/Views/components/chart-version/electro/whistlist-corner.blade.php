@props(['count' => 0])

<div id="wishlist-corner-wrapper">
    <a href="{{ url('/wishlist') }}" class="text-muted d-flex align-items-center justify-content-center me-3 position-relative">
        <span class="rounded-circle btn-md-square border">
            <i class="fas fa-heart"></i>
        </span>

        <span id="wishlist-count-badge"
              class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger {{ $count > 0 ? '' : 'd-none' }}"
              style="font-size: 0.7rem;">
            {{ $count }}
        </span>
    </a>
</div>

@once
@push('scripts')
<script>
    window.refreshWishlistUI = async function() {
        const wrapper = document.getElementById('wishlist-corner-wrapper');
        if (!wrapper) return;

        try {
            const response = await fetch("{{ route('web::web.wishlist.render') }}");
            const html = await response.text();

            wrapper.outerHTML = html;
            console.log('Corner Wishlist Updated!');
        } catch (error) {
            console.error('Gagal update corner wishlist:', error);
        }
    };
</script>
@endpush
@endonce

@props([
    'productId',
    'isWishlisted' => false,
    'class' => 'text-primary d-flex align-items-center justify-content-center me-0'
])

<style>
    .btn-wishlist { transition: transform 0.2s ease; cursor: pointer; }
    .btn-wishlist.active .wishlist-icon { color: #dc3545 !important; }
    .btn-wishlist.active span { border-color: #dc3545 !important; }
    .btn-wishlist.pe-none { pointer-events: none; opacity: 0.7; }
</style>

<a href="javascript:void(0)"
   class="{{ $class }} btn-wishlist {{ $isWishlisted ? 'active' : '' }}"
   data-id="{{ $productId }}">
    <span class="rounded-circle btn-sm-square border border-primary shadow-sm">
        <i class="{{ $isWishlisted ? 'fas' : 'far' }} fa-heart wishlist-icon"></i>
    </span>
</a>

@once
    @push('scripts')
    <script>
        document.addEventListener('click', async function (e) {
            const btn = e.target.closest('.btn-wishlist');

            if (btn && !btn.classList.contains('pe-none')) {
                e.preventDefault();
                const productId = btn.getAttribute('data-id');
                const icon = btn.querySelector('.wishlist-icon');
                const csrf = document.querySelector('meta[name="csrf-token"]');

                if (!csrf) {
                    console.error('CSRF Token missing! Pastikan ada <meta name="csrf-token"> di head');
                    return;
                }

                btn.style.transform = 'scale(1.2)';
                btn.classList.add('pe-none');
                setTimeout(() => btn.style.transform = 'scale(1)', 200);

                try {
                    const response = await fetch("{{ route('web::web.whistlist.add') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf.content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ id: productId })
                    });

                    const data = await response.json();

                    if (data.status === 'added') {
                        icon.classList.replace('far', 'fas');
                        btn.classList.add('active');
                    } else if (data.status === 'removed') {
                        icon.classList.replace('fas', 'far');
                        btn.classList.remove('active');
                    }

                    if (typeof window.refreshWishlistUI === 'function') {
                        await window.refreshWishlistUI();
                    }

                } catch (error) {
                    console.error('Error wishlist:', error);
                } finally {
                    btn.classList.remove('pe-none');
                }
            }
        });
    </script>
    @endpush
@endonce

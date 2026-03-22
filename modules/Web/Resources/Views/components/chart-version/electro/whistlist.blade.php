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
    .btn-wishlist .wishlist-icon { transition: color 0.3s ease; }
</style>

<a href="javascript:void(0)"
   class="{{ $class }} btn-wishlist {{ $isWishlisted ? 'active' : '' }}"
   data-id="{{ $productId }}"
   title="Tambah ke Favorit">
    <span class="rounded-circle btn-sm-square border border-primary shadow-sm d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
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
                    alert('CSRF Token missing! Tambahkan <meta name="csrf-token" content="{{ csrf_token() }}"> di head');
                    return;
                }

                btn.style.transform = 'scale(1.3)';
                btn.classList.add('pe-none');

                try {
                    const response = await fetch("{{ route('web::area.wishlist.toggle') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf.content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ product_id: productId })
                    });

                    if (response.status === 401) {
                        alert('Silakan login untuk menyimpan produk favorit.');
                        window.location.href = "{{ route('login') }}";
                        return;
                    }

                    const data = await response.json();

                    if (data.status === 'success') {
                        if (data.action === 'added') {
                            icon.classList.replace('far', 'fas');
                            btn.classList.add('active');
                        } else {
                            icon.classList.replace('fas', 'far');
                            btn.classList.remove('active');
                        }
                    }

                    if (typeof window.refreshWishlistUI === 'function') {
                        await window.refreshWishlistUI();
                    }

                } catch (error) {
                    console.error('Error wishlist:', error);
                } finally {
                    btn.style.transform = 'scale(1)';
                    btn.classList.remove('pe-none');
                }
            }
        });
    </script>
    @endpush
@endonce

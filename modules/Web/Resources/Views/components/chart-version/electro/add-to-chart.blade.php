@props([
    'productId',
    'class' => 'btn btn-primary border-secondary rounded-pill py-2 px-4 mb-4'
])

<a href="javascript:void(0)"
   class="{{ $class }} btn-add-to-cart"
   data-id="{{ $productId }}">
    <i class="fas fa-shopping-cart me-2"></i> Add To Cart
</a>

@once
    @push('scripts')
    <script>
        document.addEventListener('click', async function (e) {
            const btn = e.target.closest('.btn-add-to-cart');

            if (btn) {
                e.preventDefault();
                const productId = btn.getAttribute('data-id');

                const originalContent = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Adding...';
                btn.style.pointerEvents = 'none';

                try {
                    const response = await fetch("{{ route('web::web.cart.add') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ id: productId })
                    });

                    const data = await response.json();

                    if (data.success) {
                        if (typeof refreshCartUI === 'function') {
                            await refreshCartUI();
                        }
                        console.log('Produk ' + productId + ' masuk keranjang.');
                    }
                } catch (error) {
                    console.error('Error:', error);
                } finally {
                    btn.innerHTML = originalContent;
                    btn.style.pointerEvents = 'auto';
                }
            }
        });
    </script>
    @endpush
@endonce {{-- PASTIKAN INI @endonce, BUKAN @once --}}

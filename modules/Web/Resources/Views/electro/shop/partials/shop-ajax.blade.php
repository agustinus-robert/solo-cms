@push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const shopContainer = document.getElementById('shop-container');

        // Fungsi sakti untuk menggabungkan semua filter yang ada di UI
        function getCombinedUrl() {
            const baseUrl = window.location.pathname;
            const params = new URLSearchParams();

            // 1. Ambil Keyword dari Search Form
            const searchInput = document.querySelector('#shop-search-form input[name="q"]');
            if (searchInput && searchInput.value) params.set('q', searchInput.value);

            // 2. Ambil Harga dari Filter Form
            const priceInput = document.querySelector('#shop-filter-form input[name="max_price"]');
            if (priceInput && priceInput.value > 0) params.set('max_price', priceInput.value);

            // 3. Ambil Kategori Aktif (cari link kategori yang punya class fw-bold / text-primary)
            const activeCategory = document.querySelector('.ajax-filter.fw-bold');
            if (activeCategory) {
                const catUrl = new URL(activeCategory.href);
                const catId = catUrl.searchParams.get('category');
                if (catId) params.set('category', catId);
            }

            // 4. Ambil Sort dari Select
            const sortSelect = document.getElementById('sort-select');
            if (sortSelect) {
                // Karena value option kamu adalah URL lengkap, kita ambil query 'sort'-nya saja
                const sortUrl = new URL(sortSelect.value);
                const sortVal = sortUrl.searchParams.get('sort');
                if (sortVal) params.set('sort', sortVal);
            }

            return `${baseUrl}?${params.toString()}`;
        }

        async function updateShop(url) {
            if(!shopContainer) return;
            shopContainer.style.opacity = '0.5';

            try {
                const response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const html = await response.text();

                shopContainer.innerHTML = html;
                shopContainer.style.opacity = '1';

                window.history.pushState({ path: url }, '', url);

                if (typeof WOW !== 'undefined') new WOW().init();
                shopContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
            } catch (error) {
                console.error('Error Robert:', error);
                shopContainer.style.opacity = '1';
            }
        }

        // Listener Klik Kategori & Pagination
        document.addEventListener('click', function(e) {
            const link = e.target.closest('.ajax-filter, .pagination a');
            if (link) {
                e.preventDefault();

                // Jika klik "Clear Filter", langsung ke URL bersih
                if (link.classList.contains('btn-danger')) {
                    updateShop(link.href);
                    return;
                }

                // Jika klik kategori, kita update dulu visualnya baru gabungkan URL
                if (link.classList.contains('ajax-filter') && !link.classList.contains('btn-danger')) {
                    document.querySelectorAll('.ajax-filter').forEach(el => el.classList.remove('fw-bold', 'text-primary'));
                    link.classList.add('fw-bold', 'text-primary');
                    updateShop(getCombinedUrl());
                } else {
                    // Untuk pagination, biarkan pakai link.href asli agar page tidak lari
                    updateShop(link.href);
                }
            }
        });

        // Listener Form Submit (Search & Price)
        document.addEventListener('submit', function(e) {
            const form = e.target.closest('#shop-filter-form, #shop-search-form');
            if (form) {
                e.preventDefault();
                updateShop(getCombinedUrl());
            }
        });

        // Listener Sort
        const sortSelect = document.getElementById('sort-select');
        if (sortSelect) {
            sortSelect.addEventListener('change', function() {
                updateShop(getCombinedUrl());
            });
        }

        window.addEventListener('popstate', () => updateShop(window.location.href));
    });
    </script>
@endpush

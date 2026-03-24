@push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const shopContainer = document.getElementById('shop-container');
        const clearWrapper = document.getElementById('clear-filter-wrapper');

        function getCombinedUrl() {
            const baseUrl = window.location.pathname;
            const params = new URLSearchParams();

            const q = document.querySelector('input[name="q"]')?.value;
            if (q) params.set('q', q);

            const price = document.querySelector('input[name="max_price"]')?.value;
            if (price && price > 0) params.set('max_price', price);
            const activeCatLink = document.querySelector('.categories-item a.fw-bold');
            if (activeCatLink) {
                const catId = new URL(activeCatLink.href).searchParams.get('category');
                if (catId) params.set('category', catId);
            } else {
                const currentCat = new URLSearchParams(window.location.search).get('category');
                if (currentCat) params.set('category', currentCat);
            }

            const sortSelect = document.getElementById('sort-select');
            if (sortSelect) {
                const sortVal = new URL(sortSelect.value).searchParams.get('sort');
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

                updateClearFilterUI(url);

                if (typeof WOW !== 'undefined') new WOW().init();
                shopContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
            } catch (error) {
                console.error('Error:', error);
                shopContainer.style.opacity = '1';
            }
        }

        function updateClearFilterUI(url) {
            if (!clearWrapper) return;
            const params = new URL(url, window.location.origin).searchParams;
            const hasFilter = params.has('category') || params.has('q') || (params.has('max_price') && params.get('max_price') > 0);

            if (hasFilter) {
                clearWrapper.innerHTML = `
                    <div class="clear-filter mt-3">
                        <a href="${window.location.pathname}" class="btn btn-sm btn-danger w-100 ajax-filter">
                            <i class="fas fa-times me-2"></i> Clear All Filters
                        </a>
                    </div>`;
            } else {
                clearWrapper.innerHTML = '';
            }
        }

        document.addEventListener('click', function(e) {
            const link = e.target.closest('.ajax-filter, .pagination a');
            if (link) {
                e.preventDefault();

                if (link.classList.contains('btn-danger')) {
                    updateShop(window.location.pathname);
                    return;
                }

                if (link.closest('.categories-item')) {
                    document.querySelectorAll('.categories-item a').forEach(a => a.classList.remove('fw-bold', 'text-primary'));
                    link.classList.add('fw-bold', 'text-primary');
                    updateShop(getCombinedUrl());
                } else {
                    updateShop(link.href);
                }
            }
        });

        document.addEventListener('submit', function(e) {
            const form = e.target.closest('#shop-filter-form, #shop-search-form');
            if (form) {
                e.preventDefault();
                updateShop(getCombinedUrl());
            }
        });

        const sortSelect = document.getElementById('sort-select');
        if (sortSelect) {
            sortSelect.addEventListener('change', function() {
                updateShop(getCombinedUrl());
            } );
        }

        window.addEventListener('popstate', () => updateShop(window.location.href));
    });
    </script>
@endpush

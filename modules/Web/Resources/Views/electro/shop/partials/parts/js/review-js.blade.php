@push('scripts')
<script>
const dispatchGetReviews = (productId) => {
    const container = document.getElementById('review-list-container');
    const actionUrl = "{{ route('web::area.review.list', ':id') }}".replace(':id', productId);

    fetch(actionUrl, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(res => {
        if (res.data.length > 0) {
            container.innerHTML = '';

            res.data.forEach(review => {
                let stars = '';
                for (let i = 1; i <= 5; i++) {
                    stars += `<i class="fa fa-star ${i <= review.rating ? 'text-secondary' : 'text-muted'}"></i> `;
                }

                const html = `
                    <div class="d-flex mb-4 border-bottom pb-3 review-item animate-fade-in">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm"
                            style="width: 80px; height: 80px; min-width: 80px; background-color: ${review.color}; font-size: 24px;">
                            ${review.initial}
                        </div>
                        <div class="w-100 ms-4">
                            <p class="mb-2" style="font-size: 14px;">${review.date}</p>
                            <div class="d-flex justify-content-between">
                                <h5>${review.name}</h5>
                                <div class="d-flex mb-3" style="font-size: 12px;">
                                    ${stars}
                                </div>
                            </div>
                            <p class="text-dark">${review.description}</p>
                        </div>
                    </div>`;
                container.insertAdjacentHTML('beforeend', html);
            });
        } else {
            container.innerHTML = `
                <div class="text-center py-5 no-review-placeholder">
                    <i class="fa fa-comments-o fa-3x text-muted mb-3 d-block"></i>
                    <h5 class="text-muted fw-normal">Belum ada review untuk produk ini.</h5>
                </div>`;
        }
    })
    .catch(err => console.error("Gagal mengambil review:", err));
};

document.querySelector('#nav-mission-tab').addEventListener('click', function() {
    const productId = document.getElementById('review-list-container').dataset.productId;
    dispatchGetReviews(productId);
});
</script>
@endpush

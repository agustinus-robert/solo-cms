<form id="formReview" action="{{ route('web::area.review.store', $product->id) }}" method="POST">
    @csrf
    <h4 class="mb-5 fw-bold">Leave a Reply</h4>
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="border-bottom rounded">
                <input type="text" name="name" class="form-control border-0" placeholder="Your Name *" required>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="border-bottom rounded">
                <input type="email" name="email" class="form-control border-0" placeholder="Your Email *" required>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="border-bottom rounded my-4">
                <textarea name="description" class="form-control border-0" cols="30" rows="8"
                    placeholder="Your Review *" required spellcheck="false"></textarea>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="d-flex justify-content-between py-3 mb-5">
                <div class="d-flex align-items-center">
                    <p class="mb-0 me-3">Please rate:</p>
                    <div id="star-rating" class="d-flex align-items-center" style="font-size: 16px; cursor: pointer;">
                        <i class="fa fa-star star-item text-muted" data-value="1"></i>
                        <i class="fa fa-star star-item text-muted" data-value="2"></i>
                        <i class="fa fa-star star-item text-muted" data-value="3"></i>
                        <i class="fa fa-star star-item text-muted" data-value="4"></i>
                        <i class="fa fa-star star-item text-muted" data-value="5"></i>
                    </div>
                    <input type="hidden" name="rating" id="selected-rating" value="">
                </div>
                <button type="submit" id="btnSubmitReview"
                    class="btn btn-primary border border-secondary text-white rounded-pill px-4 py-3">
                    Post Comment</button>
            </div>
        </div>
    </div>
</form>

@push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('formReview');
        const starItems = document.querySelectorAll('.star-item');
        const ratingInput = document.getElementById('selected-rating');
        const btnSubmit = document.getElementById('btnSubmitReview');

        starItems.forEach(star => {
            star.addEventListener('click', function() {
                const value = this.getAttribute('data-value');
                ratingInput.value = value;

                starItems.forEach(s => {
                    if (parseInt(s.getAttribute('data-value')) <= value) {
                        s.classList.remove('text-muted');
                        s.classList.add('text-warning');
                    } else {
                        s.classList.add('text-muted');
                        s.classList.remove('text-warning');
                    }
                });
            });
        });

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            if (!ratingInput.value) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Opps!',
                    text: 'Silakan berikan rating bintang terlebih dahulu.',
                });
                return;
            }

            const formData = new FormData(form);
            const actionUrl = form.getAttribute('action');
            const originalBtnText = btnSubmit.innerText;

            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Sending...';

            fetch(actionUrl, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json().then(data => ({ status: response.status, body: data })))
            .then(res => {
                if (res.status === 200) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.body.message,
                        showConfirmButton: false,
                        timer: 2000
                    });

                    const placeholder = document.querySelector('.no-review-placeholder');
                    if (placeholder) placeholder.remove();

                    const container = document.getElementById('review-list-container');
                    if (container) {
                        let starsHtml = '';
                        for (let i = 1; i <= 5; i++) {
                            starsHtml += `<i class="fa fa-star ${i <= res.body.data.rating ? 'text-secondary' : ''}"></i> `;
                        }

                        const newReview = `
                            <div class="d-flex mb-4 border-bottom pb-3 review-item animate-fade-in">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm"
                                    style="width: 70px; height: 70px; min-width: 70px; background-color: ${res.body.data.color}; font-size: 24px;">
                                    ${res.body.data.initial}
                                </div>

                                <div class="w-100 ms-4">
                                    <p class="mb-2" style="font-size: 14px;">${res.body.data.date}</p>
                                    <div class="d-flex justify-content-between">
                                        <h5>${res.body.data.name}</h5>
                                        <div class="d-flex mb-3" style="font-size: 12px;">
                                            ${starsHtml}
                                        </div>
                                    </div>
                                    <p class="text-dark">${res.body.data.description}</p>
                                </div>
                            </div>`;

                        container.insertAdjacentHTML('afterbegin', newReview);
                    }

                    form.reset();
                    ratingInput.value = '';
                    starItems.forEach(s => {
                        s.classList.add('text-muted');
                        s.classList.remove('text-warning');
                    });

                } else if (res.status === 422) {
                    const firstError = Object.values(res.body.errors).flat()[0];
                    Swal.fire({ icon: 'error', title: 'Validasi Gagal', text: firstError });
                } else {
                    throw new Error('Server Error');
                }
            })
            .catch(error => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan, silakan coba lagi.' });
            })
            .finally(() => {
                btnSubmit.disabled = false;
                btnSubmit.innerText = originalBtnText;
            });
        });
    });
    </script>
@endpush

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
    @push('styles')
    <style>
        .variant-picker input[type="radio"] {
            display: none;
        }
        .variant-picker label {
            display: block;
            padding: 15px;
            border: 2px solid #ebedef;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            margin-bottom: 12px;
            position: relative;
        }
        .variant-picker input[type="radio"]:checked + label {
            border-color: #0d6efd;
            background-color: #f0f7ff;
        }
        .variant-picker input[type="radio"]:checked + label::after {
            content: "\f058";
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 1.2rem;
            color: #0d6efd;
        }
        .variant-picker label:hover {
            border-color: #dee2e6;
            background-color: #f8f9fa;
        }
        .modal-content { border-radius: 15px; }

        /* Qty Spinner Styling */
        .qty-input-group {
            width: 110px;
            display: flex;
            align-items: center;
        }
        .qty-input-group input {
            text-align: center;
            border-left: 0;
            border-right: 0;
            border-radius: 0;
        }
        .qty-input-group .btn {
            padding: 0px 8px;
            font-weight: bold;
        }
    </style>
    @endpush

    @push('scripts')
    <div class="modal fade" id="variantModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header border-0 bg-light">
                    <h5 class="modal-title fw-bold">Pilih Varian & Jumlah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="form-variant-selection" class="variant-picker">
                        <input type="hidden" id="modal-product-id">
                        <div id="variant-list-container"></div>
                    </form>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="button" id="btn-confirm-variant" class="btn btn-primary px-4 fw-bold">
                        Konfirmasi & Tambah
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('click', async function (e) {
            const btn = e.target.closest('.btn-add-to-cart');
            if (!btn) return;
            e.preventDefault();
            const productId = btn.getAttribute('data-id');
            await processAddToCart(productId, null, btn);
        });

        async function processAddToCart(productId, variantId = null, btnElement = null, quantity = 1) {
            let originalContent = btnElement ? btnElement.innerHTML : '';
            if (btnElement) {
                btnElement.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Adding...';
                btnElement.style.pointerEvents = 'none';
            }

            try {
                const response = await fetch("{{ route('web::web.cart.add') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        id: productId,
                        variant_id: variantId,
                        qty: quantity
                    })
                });

                const data = await response.json();
                if (data.status === 'NEED_VARIANT') {
                    openVariantSelection(productId, data.variants);
                } else if (data.success) {
                    const modalEl = document.getElementById('variantModal');
                    const modalInstance = bootstrap.Modal.getInstance(modalEl);
                    if (modalInstance) modalInstance.hide();
                    if (typeof refreshCartUI === 'function') await refreshCartUI();
                }
            } catch (error) {
                console.error('Error:', error);
            } finally {
                if (btnElement) {
                    btnElement.innerHTML = originalContent;
                    btnElement.style.pointerEvents = 'auto';
                }
            }
        }

        function openVariantSelection(productId, variants) {
            const container = document.getElementById('variant-list-container');
            document.getElementById('modal-product-id').value = productId;

            let html = '';
            variants.forEach((v, index) => {
                let displayName = "";
                let displayPrice = 0;
                let availableStock = v.available_qty ?? 0;

                try {
                    let rawData = (typeof v.product_variant === 'string') ? JSON.parse(v.product_variant) : v.product_variant;
                    if (Array.isArray(rawData) && rawData.length > 0) {
                        displayName = rawData.map(item => item.name).join(', ');
                        displayPrice = rawData[0].price || 0;
                    }
                } catch(e) {
                    displayName = "Varian Tidak Terbaca";
                }

                html += `
                    <div class="variant-option">
                        <input type="radio" name="selected_variant" id="v_${v.id}" value="${v.id}" ${index === 0 ? 'checked' : ''}>
                        <label for="v_${v.id}">
                            <div class="d-flex flex-column text-start">
                                <span class="fs-6 fw-bold text-dark">${displayName}</span>
                                <span class="text-primary fw-bold">Rp ${new Intl.NumberFormat('id-ID').format(displayPrice)}</span>

                                <span class="text-muted small mt-1">Sisa Stok: <strong class="${availableStock <= 0 ? 'text-danger' : 'text-success'}">${availableStock}</strong></span>

                                <div class="mt-2" onclick="event.preventDefault();">
                                    <div class="input-group qty-input-group">
                                        <button class="btn btn-outline-secondary btn-sm" type="button" onclick="changeModalQty('${v.id}', -1, ${availableStock})">-</button>
                                        <input type="number" id="qty_input_${v.id}" class="form-control form-control-sm" value="1" min="1" max="${availableStock}" readonly>
                                        <button class="btn btn-outline-secondary btn-sm" type="button" onclick="changeModalQty('${v.id}', 1, ${availableStock})">+</button>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>`;
            });

            container.innerHTML = html;
            const myModal = new bootstrap.Modal(document.getElementById('variantModal'));
            myModal.show();
        }

        window.changeModalQty = function(id, amount, max) {
            const input = document.getElementById('qty_input_' + id);
            let current = parseInt(input.value);
            let next = current + amount;
            if (next >= 1 && next <= max) input.value = next;
        };

        document.getElementById('btn-confirm-variant').addEventListener('click', async function() {
            const productId = document.getElementById('modal-product-id').value;
            const selected = document.querySelector('input[name="selected_variant"]:checked');

            if (!selected) {
                alert('Pilih salah satu varian!');
                return;
            }

            const variantId = selected.value;
            const qty = document.getElementById('qty_input_' + variantId).value;

            const btn = this;
            const oldText = btn.innerText;
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin me-2"></i> Memproses...';
            btn.disabled = true;

            await processAddToCart(productId, variantId, null, qty);

            btn.innerText = oldText;
            btn.disabled = false;
        });
    </script>
    @endpush
@endonce

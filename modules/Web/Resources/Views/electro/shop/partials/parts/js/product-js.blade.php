@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    window.addEventListener('cart-updated', async function() {
        try {
            const syncRes = await fetch("{{ route('web::web.cart.check-stock') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ id: "{{ $product->id }}" })
            });

            const syncData = await syncRes.json();

            if (isVariantRequired && syncData.variants) {
                const currentCode = activeCombination ? activeCombination.code : null;
                const updatedVar = syncData.variants.find(v => String(v.code) === String(currentCode));

                if (updatedVar) {
                    const newStock = parseInt(updatedVar.qty) || 0;
                    displayStock.innerText = newStock + ' items';

                    if (activeCombination) activeCombination.qty = newStock;
                    checkStockStatus(newStock);
                }
            } else {
                const newStock = parseInt(syncData.main_stock ?? (syncData.variants?.[0]?.qty)) || 0;

                productDefaultStock = newStock;
                displayStock.innerText = newStock + ' items';

                if (activeCombination) {
                    activeCombination.qty = newStock;
                }

                checkStockStatus(newStock);
            }
        } catch (err) {
            console.error("Gagal sinkron stok:", err);
        }
    });

    const combinations = @json($groupedData['combinations'] ?? []);
    const isVariantRequired = @json(!empty($groupedData['labels']));
    let productDefaultStock = parseInt("{{ $product->stock ?? 0 }}");

    const btnAddToCart = document.getElementById('btn-add-to-cart');
    const displayPrice = document.getElementById('display-price');
    const displayStock = document.getElementById('display-stock');
    const displaySku = document.getElementById('display-sku');
    const inputQty = document.getElementById('input-qty');

    const oldBtnPlus = document.getElementById('btn-qty-plus-v2');
    const oldBtnMinus = document.getElementById('btn-qty-minus-v2');

    const btnPlus = oldBtnPlus.cloneNode(true);
    const btnMinus = oldBtnMinus.cloneNode(true);

    oldBtnPlus.replaceWith(btnPlus);
    oldBtnMinus.replaceWith(btnMinus);

    let selectedTierIds = {};
    let activeCombination = null;

    function formatRupiah(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount);
    }

    function toggleQuantityControls(enable) {
        [btnPlus, btnMinus].forEach(el => {
            if (!el) return;
            if (enable) {
                el.removeAttribute('disabled');
                el.classList.remove('opacity-50');
            } else {
                el.setAttribute('disabled', true);
                el.classList.add('opacity-50');
            }
        });
    }

    function checkStockStatus(stock) {
        if (stock <= 0) {
            inputQty.value = 0;
            toggleQuantityControls(false);

            btnAddToCart.setAttribute('disabled', true);
            btnAddToCart.innerHTML = '<i class="fa fa-times me-2"></i> Stok Habis';
            btnAddToCart.classList.remove('btn-primary');
            btnAddToCart.classList.add('btn-danger');
        } else {
            if (parseInt(inputQty.value) <= 0) inputQty.value = 1;

            toggleQuantityControls(true);

            btnAddToCart.removeAttribute('disabled');
            btnAddToCart.innerHTML = '<i class="fa fa-shopping-bag me-2"></i> Add to cart';
            btnAddToCart.classList.remove('btn-danger');
            btnAddToCart.classList.add('btn-primary');
        }
    }

    function resetAndFilterNextTier(t1Id) {
        const nextGroup = document.querySelector('[data-group-index="1"]');
        if (!nextGroup) return;

        delete selectedTierIds[1];

        nextGroup.querySelectorAll('.btn-variant-option').forEach(btn => {
            btn.classList.remove('btn-primary', 'text-white');
            btn.classList.add('btn-light');
            btn.style.display = "none";
            btn.setAttribute('disabled', true);
        });

        const availableT2Ids = combinations
            .filter(c => String(c.t1) === String(t1Id))
            .map(c => String(c.t2));

        nextGroup.querySelectorAll('.btn-variant-option').forEach(btn => {
            if (availableT2Ids.includes(String(btn.dataset.tierId))) {
                btn.style.display = "inline-block";
                btn.removeAttribute('disabled');
            }
        });
    }

    function matchCombination() {
        const variantGroups = document.querySelectorAll('.variant-group');

        if (Object.keys(selectedTierIds).length < variantGroups.length) {
            toggleQuantityControls(false);
            btnAddToCart.setAttribute('disabled', true);
            return;
        }

        activeCombination = combinations.find(c => {
            return String(c.t1) === String(selectedTierIds[0]) &&
                   (selectedTierIds[1] ? String(c.t2) === String(selectedTierIds[1]) : true);
        });

        if (activeCombination) {
            displayPrice.innerText = formatRupiah(activeCombination.price);
            displayStock.innerText = activeCombination.qty + ' items';
            displaySku.innerText = activeCombination.code;

            checkStockStatus(parseInt(activeCombination.qty));
        }
    }

    if (!isVariantRequired && combinations.length > 0) {
        activeCombination = combinations[0];
        productDefaultStock = parseInt(activeCombination.qty);

        displayPrice.innerText = formatRupiah(activeCombination.price);
        displayStock.innerText = activeCombination.qty + ' items';
        displaySku.innerText = activeCombination.code;
    }

    checkStockStatus(productDefaultStock);

    document.querySelectorAll('.btn-variant-option').forEach(button => {
        button.addEventListener('click', function () {

            const group = this.closest('.variant-group');
            const groupIndex = parseInt(group.dataset.groupIndex);

            group.querySelectorAll('.btn-variant-option').forEach(btn => {
                btn.classList.remove('btn-primary', 'text-white');
                btn.classList.add('btn-light');
            });

            this.classList.remove('btn-light');
            this.classList.add('btn-primary', 'text-white');

            selectedTierIds[groupIndex] = this.dataset.tierId;

            if (groupIndex === 0) {
                resetAndFilterNextTier(this.dataset.tierId);
            }

            matchCombination();
        });
    });

    btnPlus.addEventListener('click', function (e) {
        e.preventDefault();

        if (this.hasAttribute('disabled')) return;

        let maxStock = isVariantRequired
            ? (activeCombination ? parseInt(activeCombination.qty) : 0)
            : productDefaultStock;

        let currentVal = parseInt(inputQty.value) || 0;

        if (currentVal >= maxStock) return;

        inputQty.value = currentVal + 1;
    });

    btnMinus.addEventListener('click', function (e) {
        e.preventDefault();

        if (this.hasAttribute('disabled')) return;

        let currentVal = parseInt(inputQty.value) || 0;

        if (currentVal > 1) {
            inputQty.value = currentVal - 1;
        }
    });

    inputQty.addEventListener('change', function () {
        let maxStock = isVariantRequired
            ? (activeCombination ? parseInt(activeCombination.qty) : 0)
            : productDefaultStock;

        let val = parseInt(this.value) || 1;

        if (val > maxStock) this.value = maxStock;
        if (val < 1) this.value = 1;
    });

    btnAddToCart.onclick = async function () {

        const original = this.innerHTML;
        this.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Loading...';
        this.style.pointerEvents = 'none';

        try {
            const res = await fetch("{{ route('web::web.cart.add-on-detail') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    id: "{{ $product->id }}",
                    variant_code: activeCombination ? activeCombination.code : null,
                    qty: inputQty.value
                })
            });

            const data = await res.json();

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    timer: 1500,
                    showConfirmButton: false
                });

                try {
                    const syncRes = await fetch("{{ route('web::web.cart.add-on-detail') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            id: "{{ $product->id }}",
                            variant_code: activeCombination ? activeCombination.code : null,
                            qty: 0
                        })
                    });

                    const syncData = await syncRes.json();

                    if (syncData.variants) {
                        const updatedVar = syncData.variants.find(v => v.code === (activeCombination ? activeCombination.code : null));

                        if (updatedVar) {
                            const newStock = parseInt(updatedVar.qty);

                            displayStock.innerText = newStock + ' items';
                            if (activeCombination) {
                                activeCombination.qty = newStock;
                            }

                            checkStockStatus(newStock);
                        }
                    }
                } catch (syncErr) {
                    console.error("Gagal sinkron stok:", syncErr);
                }

                if (typeof refreshCartUI === 'function') {
                    await refreshCartUI();
                }
            } else {
                throw new Error(data.message || 'Gagal');
            }

        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: error.message
            });
        } finally {
            this.innerHTML = original;
            this.style.pointerEvents = 'auto';
        }
    };
});
</script>

@endpush

@push('scripts')
<script>
    let cart = [];

    function addItemToCart(product, variant) {
        if (variant.real_stock <= 0) {
            alert('Stok habis!');
            return;
        }
        const existingItem = cart.find(item => item.variant_code === variant.code);
        if (existingItem) {
            if (existingItem.qty < variant.real_stock) {
                existingItem.qty += 1;
            } else {
                alert('Stok tidak mencukupi!');
                return;
            }
        } else {
            cart.push({
                product_id: product.id,
                product_name: product.name,
                variant_code: variant.code,
                variant_name: variant.name,
                price: parseFloat(variant.price || product.price),
                qty: 1,
                real_stock: variant.real_stock
            });
        }
        renderCart();
    }

    function renderCart() {
        const tbody = document.getElementById('selectedItemsBody');
        if(!tbody) return;
        tbody.innerHTML = '';
        if (cart.length === 0) {
            tbody.innerHTML = '<tr><td colspan="3" class="text-center py-5 text-muted small">Belum ada barang dipilih.</td></tr>';
            updateSummary();
            return;
        }
        cart.forEach((item, index) => {
            const total = item.price * item.qty;
            const row = `
                <tr>
                    <td class="px-3 py-2 col-produk">
                        <div class="cart-item-title text-truncate" title="${item.product_name}">${item.product_name}</div>
                        <div class="cart-item-sub small text-muted">${item.variant_name !== 'Default' ? item.variant_name : item.variant_code}</div>
                    </td>
                    <td class="text-center py-2 col-qty">
                        <div class="d-flex align-items-center justify-content-center gap-1">
                            <button type="button" class="btn btn-xs btn-light border p-0" style="width:20px; height:20px;" onclick="updateQty(${index}, -1)">-</button>
                            <span class="fw-bold small" style="min-width: 15px;">${item.qty}</span>
                            <button type="button" class="btn btn-xs btn-light border p-0" style="width:20px; height:20px;" onclick="updateQty(${index}, 1)">+</button>
                        </div>
                    </td>
                    <td class="text-end px-3 py-2 col-total">
                        <div class="d-flex align-items-center justify-content-end">
                            <span class="fw-bold small text-primary">Rp${new Intl.NumberFormat('id-ID').format(total)}</span>
                            <button type="button" class="btn btn-link text-danger p-0 ms-2" onclick="removeItem(${index})">
                                <i class="fa fa-times-circle"></i>
                            </button>
                        </div>
                    </td>
                </tr>`;
            tbody.insertAdjacentHTML('beforeend', row);
        });
        updateSummary();
    }

    function updateQty(index, delta) {
        const item = cart[index];
        if (item.qty + delta > 0) {
            if (delta > 0 && item.qty >= item.real_stock) {
                alert('Maksimal stok tercapai');
                return;
            }
            item.qty += delta;
        } else {
            removeItem(index);
        }
        renderCart();
    }

    function removeItem(index) {
        cart.splice(index, 1);
        renderCart();
    }

   function updateSummary() {
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
        const inputDisc = document.getElementById('inputDiscount');
        const discount = (inputDisc && inputDisc.value !== "") ? parseFloat(inputDisc.value) : 0;

        const afterDiscount = Math.max(0, subtotal - discount);
        const ppn = afterDiscount * 0.11;
        const grandTotal = afterDiscount + ppn;

        const elSub = document.getElementById('textSubtotal');
        const elGrand = document.getElementById('textGrandTotal');
        const btnSub = document.getElementById('btnSubmit');

        if(elSub) elSub.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(subtotal);
        if(elGrand) elGrand.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(grandTotal);

        if(btnSub) btnSub.disabled = (cart.length === 0);

        calculateChange(grandTotal);
    }

    function calculateChange(forcedGrandTotal = null) {
        let gt = forcedGrandTotal;
        if (gt === null) {
            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
            const discInput = document.getElementById('inputDiscount');
            const disc = (discInput && discInput.value !== "") ? parseFloat(discInput.value) : 0;
            gt = Math.max(0, subtotal - disc);
        }
        const inputPaid = document.getElementById('amountPaid');
        const amountPaid = (inputPaid && inputPaid.value !== "") ? parseFloat(inputPaid.value) : 0;
        const change = amountPaid - gt;
        const textChange = document.getElementById('textChange');
        if(!textChange) return;

        textChange.innerText = (amountPaid === 0) ? 'Rp 0' : 'Rp ' + new Intl.NumberFormat('id-ID').format(change);
        textChange.className = (change < 0) ? 'fw-bold text-danger' : 'fw-bold text-success';
    }

    function initSearch() {
        const searchInput = document.getElementById('searchInput');
        if (!searchInput) return;

        searchInput.addEventListener('input', function() {
            const keyword = this.value.toLowerCase().trim();
            const productColumns = document.querySelectorAll('.pos-left-section .row.g-2 > div:not(#noProductMsg)');
            let hasVisible = 0;

            productColumns.forEach(column => {
                const titleEl = column.querySelector('h6');
                if (!titleEl) return;

                const productName = titleEl.innerText.toLowerCase();

                if (productName.includes(keyword)) {
                    column.classList.remove('d-none');
                    setTimeout(() => {
                        column.style.opacity = "1";
                        column.style.transform = "scale(1)";
                    }, 10);
                    hasVisible++;
                } else {
                    column.style.opacity = "0";
                    column.style.transform = "scale(0.9)";
                    setTimeout(() => {
                        if (column.style.opacity === "0") column.classList.add('d-none');
                    }, 300);
                }
            });

            toggleNoProductMessage(hasVisible, keyword);
        });
    }

    function toggleNoProductMessage(count, keyword) {
        const container = document.querySelector('.pos-left-section .row.g-2');
        let msg = document.getElementById('noProductMsg');

        if (count === 0) {
            if (!msg) {
                const html = `
                    <div id="noProductMsg" class="col-12 text-center py-5 my-5 animate__animated animate__fadeIn" style="width: 100%;">
                        <i class="fa fa-box-open fa-3x text-light mb-3"></i>
                        <h5 class="text-muted fw-bold">Barang tidak ada</h5>
                        <p class="small text-secondary">Produk "${keyword}" tidak ditemukan.</p>
                    </div>`;
                container.insertAdjacentHTML('beforeend', html);
            }
        } else {
            if (msg) msg.remove();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const inputDisc = document.getElementById('inputDiscount');
        const inputPaid = document.getElementById('amountPaid');
        const paymentType = document.getElementById('paymentType');

        if(inputDisc) inputDisc.addEventListener('input', updateSummary);
        if(inputPaid) inputPaid.addEventListener('input', () => calculateChange());
        if(paymentType) paymentType.addEventListener('change', () => updateSummary());
        const posForm = document.getElementById('saleForm');

        if (posForm) {
            posForm.addEventListener('submit', function(e) {
                if (cart.length === 0) {
                    e.preventDefault();
                    alert('Keranjang masih kosong!');
                    return;
                }

                const formattedItems = cart.map(item => {
                    return {
                        id: item.product_id,
                        bought_variants: [{
                            code: item.variant_code,
                            qty: item.qty,
                            price: item.price
                        }]
                    };
                });

                const itemsInput = document.getElementById('itemsInput');
                if (itemsInput) {
                    itemsInput.value = JSON.stringify(formattedItems);
                    console.log("Data items berhasil disiapkan:", itemsInput.value);
                } else {
                    console.error("Elemen itemsInput tidak ditemukan!");
                }
            });
        }

        initSearch();
        renderCart();
    });
</script>
@endpush

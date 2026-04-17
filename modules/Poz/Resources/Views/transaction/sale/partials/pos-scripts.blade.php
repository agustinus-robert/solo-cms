@push('scripts')
<script>
    let cart = [];

    function broadcastStockToClients(vCode, vStock) {
        if (window.Echo && window.Echo.connector.pusher) {
            window.Echo.connector.pusher.send_event('client-stock-reserved', {
                variantCode: vCode,
                newVisualStock: vStock,
                socketId: window.Echo.socketId()
            }, 'products-market');
        }
    }

    function updateVisualStock(variantCode, newStock) {
        const buttons = document.querySelectorAll(`.btn-add-to-cart[data-variant-code="${variantCode}"]`);

        buttons.forEach(btn => {
            btn.innerText = `Stok: ${newStock}`;

            if (newStock <= 0) {
                btn.classList.add('disabled', 'btn-light');
                btn.classList.remove('btn-outline-primary');

                const card = btn.closest('.product-card-item');
                if (card) {
                    card.classList.add('is-out');
                    if (!card.querySelector('.bg-danger')) {
                        card.insertAdjacentHTML('afterbegin', '<div class="position-absolute top-50 start-50 translate-middle bg-danger text-white px-2 py-1 rounded small fw-bold" style="z-index: 10;">HABIS</div>');
                    }
                }
            } else {
                btn.classList.remove('disabled', 'btn-light');
                btn.classList.add('btn-outline-primary');
                const card = btn.closest('.product-card-item');
                if (card) {
                    card.classList.remove('is-out');
                    const badge = card.querySelector('.bg-danger');
                    if (badge) badge.remove();
                }
            }
        });
    }

    function addItemToCart(product, variant) {
        if (variant.real_stock <= 0) {
            alert('Stok habis!');
            return;
        }

        const existingItem = cart.find(item => item.variant_code === variant.code);
        let currentQtyInCart = existingItem ? existingItem.qty : 0;

        if (currentQtyInCart < variant.real_stock) {
            if (existingItem) {
                existingItem.qty += 1;
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

            let newQty = existingItem ? existingItem.qty : 1;
            let currentVisualStock = variant.real_stock - newQty;

            // Update layar sendiri
            updateVisualStock(variant.code, currentVisualStock);
            renderCart();

            // Beritahu client lain
            broadcastStockToClients(variant.code, currentVisualStock);
        } else {
            alert('Stok tidak mencukupi!');
        }
    }

    function updateQty(index, delta) {
        const item = cart[index];
        if (delta > 0 && item.qty >= item.real_stock) {
            alert('Maksimal stok tercapai');
            return;
        }

        if (item.qty + delta > 0) {
            item.qty += delta;
            let newVisualStock = item.real_stock - item.qty;

            updateVisualStock(item.variant_code, newVisualStock);
            broadcastStockToClients(item.variant_code, newVisualStock);
        } else {
            removeItem(index);
            return;
        }
        renderCart();
    }

    function removeItem(index) {
        const item = cart[index];
        const vCode = item.variant_code;
        const vStock = item.real_stock;

        // Kembalikan stok visual dan broadcast
        updateVisualStock(vCode, vStock);
        broadcastStockToClients(vCode, vStock);

        cart.splice(index, 1);
        renderCart();
    }

    function syncInternalProductStock(variantCode, newStock) {
        const itemInCart = cart.find(item => item.variant_code === variantCode);
        if (itemInCart) {
            itemInCart.real_stock = newStock;
            if (itemInCart.qty > newStock) {
                itemInCart.qty = Math.max(0, newStock);
                if (itemInCart.qty === 0) {
                    cart.splice(cart.indexOf(itemInCart), 1);
                }
            }
            renderCart();
        }
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

    function updateSummary() {
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
        const inputDisc = document.getElementById('inputDiscount');
        const discount = (inputDisc && inputDisc.value !== "") ? parseFloat(inputDisc.value) : 0;

        const afterDiscount = Math.max(0, subtotal - discount);
        const ppn = afterDiscount * 0.11;
        const grandTotal = afterDiscount + ppn;

        const elSub = document.getElementById('textSubtotal');
        const elPPN = document.getElementById('textPPN');
        const elGrand = document.getElementById('textGrandTotal');
        const btnSubmit = document.getElementById('btnSubmit');

        const formatter = new Intl.NumberFormat('id-ID');

        if(elSub) elSub.innerText = 'Rp ' + formatter.format(subtotal);
        if(elPPN) elPPN.innerText = 'Rp ' + formatter.format(ppn);
        if(elGrand) elGrand.innerText = 'Rp ' + formatter.format(grandTotal);

        if(btnSubmit) btnSubmit.disabled = (cart.length === 0);

        calculateChange(grandTotal);
    }

    function calculateChange(forcedGrandTotal = null) {
        let gt = forcedGrandTotal;
        if (gt === null) {
            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
            const discInput = document.getElementById('inputDiscount');
            const disc = (discInput && discInput.value !== "") ? parseFloat(discInput.value) : 0;
            const afterDiscount = Math.max(0, subtotal - disc);
            gt = afterDiscount + (afterDiscount * 0.11);
        }

        const inputPaid = document.getElementById('amountPaid');
        const amountPaid = (inputPaid && inputPaid.value !== "") ? parseFloat(inputPaid.value) : 0;

        const change = amountPaid - gt;
        const textChange = document.getElementById('textChange');

        if(!textChange) return;

        if (amountPaid === 0) {
            textChange.innerText = 'Rp 0';
            textChange.className = 'fw-bold text-success';
        } else {
            textChange.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(change);
            textChange.className = change < 0 ? 'fw-bold text-danger' : 'fw-bold text-success';
        }
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
                    column.style.opacity = "1";
                    column.style.transform = "scale(1)";
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
                container.insertAdjacentHTML('beforeend', `
                    <div id="noProductMsg" class="col-12 text-center py-5 my-5" style="width: 100%;">
                        <i class="fa fa-box-open fa-3x text-light mb-3"></i>
                        <h5 class="text-muted fw-bold">Barang tidak ada</h5>
                        <p class="small text-secondary">Produk "${keyword}" tidak ditemukan.</p>
                    </div>`);
            }
        } else {
            if (msg) msg.remove();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // --- HANDLER UNTUK SINKRONISASI MASUK ---
        if (window.Echo) {
            const marketChannel = window.Echo.channel('products-market');

            // Listen broadcast dari server
            marketChannel.listen('.stock.updated', (data) => {
                updateVisualStock(data.variantCode, data.newStock);
                syncInternalProductStock(data.variantCode, data.newStock);
            });

            // Listen client-event dari POS lain
            marketChannel.on('client-stock-reserved', (data) => {
                if (data.socketId !== window.Echo.socketId()) {
                    updateVisualStock(data.variantCode, data.newVisualStock);
                }
            });
        }

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

                const formattedItems = cart.map(item => ({
                    id: item.product_id,
                    bought_variants: [{
                        code: item.variant_code,
                        qty: item.qty,
                        price: item.price
                    }]
                }));

                const itemsInput = document.getElementById('itemsInput');
                if (itemsInput) itemsInput.value = JSON.stringify(formattedItems);
            });
        }

        initSearch();
        renderCart();
    });
</script>
@endpush

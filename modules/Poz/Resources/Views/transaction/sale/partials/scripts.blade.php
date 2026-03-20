<script>
document.addEventListener('DOMContentLoaded', function() {
    const products = @json($products);
    const allStocks = @json($stocks);
    let selectedItems = @json($selectedItems ?? []);

    const getEl = (id) => document.getElementById(id);
    const itemsBody = getEl('selectedItemsBody');
    const searchInput = getEl('searchInput');
    const suggestionBox = getEl('suggestionBox');
    const suggestionList = getEl('suggestionList');

    const inputDiscount = getEl('inputDiscount');
    const amountPaid = getEl('amountPaid');
    const paymentType = getEl('paymentType');
    const textSubtotal = getEl('textSubtotal');
    const textGrandTotal = getEl('textGrandTotal');
    const textChange = getEl('textChange');
    const btnSubmit = getEl('btnSubmit');
    const cashRegisterWrapper = document.querySelector('.alert-info')?.closest('.col-12'); // Wrapper saldo

    if (!itemsBody) return;

    function formatCurrency(val) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(val));
    }

    function getRealStock(variantCode) {
        if (!allStocks || allStocks.length === 0) return 0;
        const mutations = allStocks.filter(s => s.variant_code === variantCode);
        const result = mutations.reduce((total, s) => {
            const qty = parseInt(s.qty) || 0;
            return s.status === 'plus' ? total + qty : total - qty;
        }, 0);
        return result < 0 ? 0 : result;
    }

    function calculateSummary() {
        let subtotal = 0;
        selectedItems.forEach(product => {
            product.bought_variants.forEach(v => {
                subtotal += (v.price * v.qty);
            });
        });

        const discount = parseInt(inputDiscount?.value) || 0;
        const grandTotal = Math.max(0, subtotal - discount);
        const paid = parseInt(amountPaid?.value) || 0;
        const change = paid - grandTotal;

        if(textSubtotal) textSubtotal.innerText = formatCurrency(subtotal);
        if(textGrandTotal) textGrandTotal.innerText = formatCurrency(grandTotal);
        if(textChange) textChange.innerText = formatCurrency(change > 0 ? change : 0);

        if (cashRegisterWrapper) {
            cashRegisterWrapper.style.display = (paymentType.value === 'cash') ? 'block' : 'none';
        }

        if(btnSubmit) {
            const isCash = paymentType?.value === 'cash';
            const hasItems = selectedItems.length > 0;
            const enoughMoney = isCash ? (paid >= grandTotal) : true;
            btnSubmit.disabled = !(hasItems && enoughMoney);
        }
    }

    function handleSearch() {
        const query = searchInput.value.toLowerCase().trim();
        suggestionList.innerHTML = '';

        if (query.length < 1) {
            suggestionBox.classList.add('d-none');
            return;
        }

        const filtered = products.filter(p => p.name.toLowerCase().includes(query)).slice(0, 15);
        if (filtered.length === 0) {
            suggestionList.innerHTML = '<div class="p-5 text-center text-muted">Barang tidak ditemukan...</div>';
            suggestionBox.classList.remove('d-none');
            return;
        }

        filtered.forEach(p => {
            let allVariants = [];
            if (p.variant) {
                p.variant.forEach(vRow => {
                    const vData = typeof vRow.product_variant === 'string' ? JSON.parse(vRow.product_variant) : vRow.product_variant;
                    if (Array.isArray(vData)) {
                        vData.forEach(v => {
                            if (v.status !== 'deleted' && v.deleted_at === null) allVariants.push(v);
                        });
                    }
                });
            }

            const isSingle = allVariants.length === 1 && (allVariants[0].variant_type === 'no_variant' || allVariants[0].name.toLowerCase() === 'default');
            const item = document.createElement('div');

            if (isSingle) {
                const v = allVariants[0];
                const stockQty = getRealStock(v.code);
                const isOut = stockQty <= 0;
                item.className = `list-group-item list-group-item-action p-3 border-bottom select-v-btn ${isOut ? 'disabled bg-light' : ''}`;
                item.dataset.pid = p.id; item.dataset.vjson = JSON.stringify(v); item.dataset.realqty = stockQty;
                item.innerHTML = `<div class="d-flex justify-content-between align-items-center">
                    <div class="fw-bold text-dark text-uppercase">${p.name}</div>
                    <div class="text-end"><span class="badge ${isOut ? 'bg-secondary' : 'bg-primary'}">${stockQty}</span></div>
                </div>`;
            } else {
                item.className = 'list-group-item p-3 border-bottom';
                let btns = '';
                allVariants.forEach(v => {
                    const sQty = getRealStock(v.code);
                    btns += `<button type="button" class="btn btn-sm ${sQty<=0?'btn-light':'btn-outline-primary'} me-2 mb-2 select-v-btn" data-pid="${p.id}" data-realqty="${sQty}" data-vjson='${JSON.stringify(v)}' ${sQty<=0?'disabled':''}>${v.name}: ${sQty}</button>`;
                });
                item.innerHTML = `<div class="fw-bold text-dark mb-2 text-uppercase small">${p.name}</div><div class="d-flex flex-wrap">${btns}</div>`;
            }
            suggestionList.appendChild(item);
        });
        suggestionBox.classList.remove('d-none');
    }

    function addItemToCart(product, variant) {
        let existingRow = selectedItems.find(i => i.id == product.id);
        const isDefault = variant.variant_type === 'no_variant' || variant.name.toLowerCase() === 'default';
        const vObj = {
            code: variant.code,
            name: isDefault ? 'Produk Utama' : variant.name,
            price: parseInt(variant.price),
            qty: 1,
            maxStock: parseInt(variant.qty)
        };

        if (existingRow) {
            let vExist = existingRow.bought_variants.find(v => v.code === variant.code);
            if (vExist) {
                if (vExist.qty < vObj.maxStock) vExist.qty++;
                else alert('Stok Habis!');
            } else {
                existingRow.bought_variants.push(vObj);
            }
        } else {
            selectedItems.push({ id: product.id, name: product.name, bought_variants: [vObj] });
        }
        renderTable();
    }

    function renderTable() {
        itemsBody.innerHTML = '';
        if (selectedItems.length === 0) {
            itemsBody.innerHTML = '<tr><td colspan="5" class="text-center py-5 text-muted small">Belum ada barang dipilih.</td></tr>';
            calculateSummary();
            return;
        }

        selectedItems.forEach((product, pIdx) => {
            let variantHtml = '';
            let tQty = 0, tPrice = 0;

            product.bought_variants.forEach((v, vIdx) => {
                tQty += v.qty;
                tPrice += (v.price * v.qty);
                variantHtml += `
                    <div class="d-flex justify-content-between align-items-center bg-white p-2 mb-1 rounded border shadow-sm small">
                        <div><b>${v.name}</b><br><span class="text-primary">${formatCurrency(v.price)}</span></div>
                        <div class="d-flex align-items-center gap-2">
                            <input type="number" class="form-control form-control-sm text-center update-qty" style="width: 55px" data-p="${pIdx}" data-v="${vIdx}" value="${v.qty}">
                            <button type="button" class="btn btn-sm text-danger remove-v" data-p="${pIdx}" data-v="${vIdx}">×</button>
                        </div>
                        <input type="hidden" name="items[${pIdx}][variants][${vIdx}][code]" value="${v.code}">
                        <input type="hidden" name="items[${pIdx}][variants][${vIdx}][qty]" value="${v.qty}">
                        <input type="hidden" name="items[${pIdx}][variants][${vIdx}][price]" value="${v.price}">
                    </div>`;
            });

            itemsBody.insertAdjacentHTML('beforeend', `
                <tr class="align-middle">
                    <td><div class="fw-bold text-uppercase mb-1">${product.name}</div>${variantHtml}</td>
                    <td class="text-center fw-bold h5">${tQty}</td>
                    <td class="text-end fw-bold text-primary">${formatCurrency(tPrice)}</td>
                    <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-row" data-p="${pIdx}"><i class="fa fa-trash"></i></button></td>
                </tr>`);
        });
        calculateSummary();
    }

    if (searchInput) searchInput.addEventListener('input', handleSearch);
    suggestionList.addEventListener('click', (e) => {
        const target = e.target.closest('.select-v-btn');
        if (!target || target.classList.contains('disabled')) return;
        const variant = JSON.parse(target.dataset.vjson);
        variant.qty = parseInt(target.dataset.realqty);
        addItemToCart(products.find(p => p.id == target.dataset.pid), variant);
        searchInput.value = ''; suggestionBox.classList.add('d-none');
    });

    [inputDiscount, amountPaid, paymentType].forEach(el => {
        if(el) {
            el.addEventListener('input', calculateSummary);
            el.addEventListener('change', calculateSummary);
        }
    });

    itemsBody.addEventListener('input', (e) => {
        if (e.target.classList.contains('update-qty')) {
            const p = e.target.dataset.p, v = e.target.dataset.v;
            let val = parseInt(e.target.value), data = selectedItems[p].bought_variants[v];
            if (val > data.maxStock) val = data.maxStock;
            if (val < 1 || isNaN(val)) val = 1;
            data.qty = val; renderTable();
        }
    });

    itemsBody.addEventListener('click', (e) => {
        const btnV = e.target.closest('.remove-v'), btnR = e.target.closest('.remove-row');
        if (btnV) {
            selectedItems[btnV.dataset.p].bought_variants.splice(btnV.dataset.v, 1);
            if (selectedItems[btnV.dataset.p].bought_variants.length === 0) selectedItems.splice(btnV.dataset.p, 1);
            renderTable();
        }
        if (btnR) { selectedItems.splice(btnR.dataset.p, 1); renderTable(); }
    });

    renderTable();
});
</script>

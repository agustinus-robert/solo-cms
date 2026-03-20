<script>
document.addEventListener('DOMContentLoaded', function() {
    const products = @json($products);
    let selectedItems = @json($selectedItems ?? []);

    const searchInput = document.getElementById('searchInput');
    const suggestionBox = document.getElementById('suggestionBox');
    const suggestionList = document.getElementById('suggestionList');
    const itemsBody = document.getElementById('selectedItemsBody');

    const paymentType = document.getElementById('paymentType');
    const cashRegisterWrapper = document.getElementById('cashRegisterWrapper');
    const cashPaymentWrapper = document.getElementById('cashPaymentWrapper');
    const amountPaidInput = document.getElementById('amountPaid');
    const dueDateWrapper = document.getElementById('dueDateWrapper');
    const dueDateInput = document.getElementById('dueDate');

    const cashBalance = parseFloat(document.getElementById('rawCashBalance').value || 0);
    const cashAlert = document.getElementById('cashAlert');
    const btnSubmit = document.getElementById('btnSubmit');

    document.querySelectorAll('.product-card-item').forEach(card => {
        card.addEventListener('click', function() {
            const productId = this.dataset.id;
            const productData = products.find(p => p.id == productId);

            if (productData) {
                selectProduct(productData);
            }
        });
    });

    if (window.location.search.includes('pos=true')) {
        document.body.style.overflow = 'hidden';
    }

    function renderTable() {
        itemsBody.innerHTML = '';
        if (selectedItems.length === 0) {
            itemsBody.innerHTML = '<tr><td colspan="5" class="text-center py-5 text-muted small">Keranjang belanja kosong.</td></tr>';
            btnSubmit.disabled = true;
        } else {
            selectedItems.forEach((item, index) => {
                const row = `
                    <tr>
                        <td>
                            <div class="fw-bold text-dark">${item.name}</div>
                            <small class="text-muted">Maks: ${item.maxStock}</small>
                            <input type="hidden" name="items[${index}][id]" value="${item.id}">
                            <input type="hidden" name="items[${index}][price]" value="${item.price}">
                        </td>
                        <td>
                            <input type="number" name="items[${index}][qty]" class="form-control form-control-sm text-center fw-bold input-qty" data-index="${index}" value="${item.qty}" min="1">
                        </td>
                        <td class="text-end text-muted">${formatCurrency(item.price)}</td>
                        <td class="text-end fw-bold text-primary">${formatCurrency(item.qty * item.price)}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-link text-danger p-0 btn-remove" data-index="${index}"><i class="fa fa-trash-alt"></i></button>
                        </td>
                    </tr>`;
                itemsBody.insertAdjacentHTML('beforeend', row);
            });
        }
        calculateAll();
    }

    function calculateAll() {
        const subtotal = selectedItems.reduce((acc, item) => acc + (item.qty * item.price), 0);
        const discount = parseInt(document.getElementById('inputDiscount').value) || 0;
        const taxable = Math.max(0, subtotal - discount);
        const ppn = taxable * 0.11;
        const grandTotal = taxable + ppn;

        document.getElementById('textSubtotal').innerText = formatCurrency(subtotal);
        document.getElementById('textPpn').innerText = formatCurrency(ppn);
        document.getElementById('textGrandTotal').innerText = formatCurrency(grandTotal);

        btnSubmit.disabled = (selectedItems.length === 0);

        if (paymentType.value === 'cash') {
            const bayar = parseFloat(amountPaidInput.value) || 0;
            const kembalian = bayar - grandTotal;
            document.getElementById('textChange').innerText = formatCurrency(Math.max(0, kembalian));

            if (bayar < grandTotal) {
                btnSubmit.disabled = true;
                cashAlert.classList.add('d-none');
            } else if (kembalian > cashBalance) {
                btnSubmit.disabled = true;
                cashAlert.classList.remove('d-none');
            } else {
                cashAlert.classList.add('d-none');
            }
        } else {
            cashAlert.classList.add('d-none');
        }
    }

    // Toggle Tampilan Berdasarkan Tipe Pembayaran
    paymentType.addEventListener('change', function() {
        const isCash = (this.value === 'cash');

        cashRegisterWrapper.classList.toggle('d-none', !isCash);
        cashPaymentWrapper.classList.toggle('d-none', !isCash);
        dueDateWrapper.classList.toggle('d-none', isCash);

        if (isCash) {
            dueDateInput.value = '';
            dueDateInput.required = false;
        } else {
            dueDateInput.required = true;
        }
        calculateAll();
    });

    // Search & Selection Logic
    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        suggestionList.innerHTML = '';
        if (query.length < 1) { suggestionBox.classList.add('d-none'); return; }

        const filtered = products.filter(p => p.name.toLowerCase().includes(query)).slice(0, 5);
        if (filtered.length > 0) {
            filtered.forEach(p => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3';
                btn.innerHTML = `<div><div class="fw-bold text-dark">${p.name}</div><small>Stok: ${p.stok_tersedia}</small></div><span>${formatCurrency(p.price)}</span>`;
                btn.onclick = () => {
                    if (parseInt(p.stok_tersedia) <= 0) { alert('Stok kosong.'); return; }
                    const existing = selectedItems.find(i => i.id == p.id);
                    if (existing) { if(existing.qty < p.stok_tersedia) existing.qty++; }
                    else { selectedItems.push({ id: p.id, name: p.name, price: parseInt(p.price), maxStock: p.stok_tersedia, qty: 1 }); }
                    searchInput.value = ''; suggestionBox.classList.add('d-none'); renderTable();
                };
                suggestionList.appendChild(btn);
            });
            suggestionBox.classList.remove('d-none');
        } else { suggestionBox.classList.add('d-none'); }
    });

    itemsBody.addEventListener('input', (e) => {
        if (e.target.classList.contains('input-qty')) {
            const index = e.target.dataset.index;
            let val = parseInt(e.target.value);
            if (val > selectedItems[index].maxStock) { alert('Stok tidak mencukupi'); val = selectedItems[index].maxStock; }
            selectedItems[index].qty = val || 1;
            renderTable();
        }
    });

    itemsBody.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-remove');
        if (btn) { selectedItems.splice(btn.dataset.index, 1); renderTable(); }
    });

    document.getElementById('inputDiscount').addEventListener('input', calculateAll);
    amountPaidInput.addEventListener('input', calculateAll);

    function formatCurrency(val) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(val)); }

    renderTable();
});
</script>

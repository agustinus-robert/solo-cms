<script>
document.addEventListener('DOMContentLoaded', () => {
    if (window.Echo) {
        window.Echo.channel('products-market')
            .listen('.stock.updated', (data) => {
                console.log("--- START REAL-TIME SYNC ---");
                console.log("Payload data:", data);
                const isOthers = data.socketId !== window.Echo.socketId();
                const buttons = document.querySelectorAll(`.btn-add-to-cart[onclick*="${data.variantCode}"]`);
                console.log(`Mencari tombol luar (${data.variantCode}):`, buttons.length, "ditemukan");

                buttons.forEach(btn => {
                    let oldStock = 0;
                    if (btn.innerText.includes(':')) {
                        oldStock = parseInt(btn.innerText.split(':')[1]) || 0;
                    }

                    if (btn.innerText.includes(':')) {
                        let parts = btn.innerText.split(':');
                        btn.innerHTML = `${parts[0]}: ${data.newStock}`;
                    } else {
                        btn.innerHTML = `Stok: ${data.newStock}`;
                    }

                    if (isOthers && data.newStock < oldStock) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'info',
                            title: `Stok diperbarui karena ada pelanggan lain yang beli.`,
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    }

                    if (data.newStock <= 0) {
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

                const variantInput = document.querySelector(`input[data-code="${data.variantCode}"]`);
                if (variantInput) {
                    console.log("🎯 Input Radio di Modal ditemukan:", variantInput.id);

                    const label = variantInput.nextElementSibling;
                    const stockDisplay = label ? label.querySelector('strong') : null;
                    const uniqueId = `${variantInput.value}_${data.variantCode}`;
                    const qtyInput = document.getElementById(`qty_input_${uniqueId}`);

                    if (stockDisplay) {
                        console.log(`Update Teks Stok Modal: ${stockDisplay.innerText} -> ${data.newStock}`);
                        // UPDATE ANGKA SISA STOK DI DALAM MODAL
                        stockDisplay.innerText = data.newStock;
                        stockDisplay.className = data.newStock <= 0 ? 'text-danger' : 'text-success';

                        if (data.newStock <= 0) {
                            console.log("Handling stok habis di modal...");
                            variantInput.disabled = true;
                            if (variantInput.checked) variantInput.checked = false;

                            const titleArea = label.querySelector('.justify-content-between');
                            if (titleArea && !titleArea.querySelector('.badge')) {
                                titleArea.insertAdjacentHTML('beforeend', '<span class="badge bg-danger">Habis</span>');
                            }

                            const qtyContainer = label.querySelector('.mt-2');
                            if (qtyContainer) qtyContainer.remove();

                            const anyAvailable = document.querySelectorAll('input[name="selected_variant"]:not(:disabled)');
                            const btnConfirm = document.getElementById('btn-confirm-variant');
                            if (anyAvailable.length === 0 && btnConfirm) {
                                btnConfirm.disabled = true;
                                btnConfirm.innerText = 'Stok Habis';
                            }

                            if (isOthers) {
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'warning',
                                    title: 'Stok di modal baru saja habis dibeli orang lain!',
                                    showConfirmButton: false,
                                    timer: 4000
                                });
                            }
                        } else {
                            variantInput.disabled = false;
                            console.log("Stok tersedia kembali/bertambah.");

                            const badgeHabis = label.querySelector('.badge.bg-danger');
                            if (badgeHabis) badgeHabis.remove();

                            if (qtyInput) {
                                console.log(`Updating Qty Input Max: ${data.newStock}`);
                                qtyInput.max = data.newStock;
                                if (parseInt(qtyInput.value) > data.newStock) {
                                    qtyInput.value = data.newStock;
                                }

                                const qtyGroup = qtyInput.closest('.qty-input-group');
                                if (qtyGroup) {
                                    const actionButtons = qtyGroup.querySelectorAll('button');
                                    actionButtons.forEach(btn => {
                                        let currentOnClick = btn.getAttribute('onclick');
                                        if (currentOnClick && currentOnClick.includes('changeModalQty')) {
                                            let newOnClick = currentOnClick.replace(/,\s*\d+\)$/, `, ${data.newStock})`);
                                            btn.setAttribute('onclick', newOnClick);
                                            console.log("Updated Button Onclick:", newOnClick);
                                        }
                                    });
                                }
                            }

                            const btnConfirm = document.getElementById('btn-confirm-variant');
                            if (btnConfirm && btnConfirm.disabled) {
                                btnConfirm.disabled = false;
                                btnConfirm.innerText = 'Konfirmasi & Tambah';
                            }
                        }
                    } else {
                        console.warn("⚠️ Label strong (stok) tidak ditemukan!");
                    }
                } else {
                    console.warn(`⚠️ Varian dengan code ${data.variantCode} tidak ditemukan di modal yang terbuka.`);
                }
                console.log("--- END REAL-TIME SYNC ---");
            });
    }
});
</script>

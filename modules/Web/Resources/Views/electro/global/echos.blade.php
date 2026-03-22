<script>
window.Echo = new Echo({
    broadcaster: 'reverb',
    key: '{{ env("REVERB_APP_KEY") }}',
    wsHost: '{{ env("REVERB_HOST") }}',
    wsPort: {{ env("REVERB_PORT") ?? 80 }},
    wssPort: {{ env("REVERB_PORT") ?? 443 }},
    forceTLS: {{ env("REVERB_SCHEME") === 'https' ? 'true' : 'false' }},
    enabledTransports: ['ws', 'wss'],
    disableStats: true,
    authEndpoint: '/broadcasting/auth',
    namespace: 'App.Events',
});

window.Echo.connector.pusher.connection.bind('state_change', (states) => {
    console.log('Status Koneksi:', states.current);
});
</script>


<script>
window.Echo.channel('products-market')
    .listen('.stock.updated', (data) => {
        console.log("Data Diterima di Browser:", data);

        const pId = data.productId.toString();
        const vCode = data.variantCode ? data.variantCode.toString() : "";
        const newStock = parseInt(data.newStock);

        const catalogBadges = document.querySelectorAll(`.stock-display-${pId}-${vCode}`);
        catalogBadges.forEach(badge => {
            badge.innerText = newStock;
            if (newStock <= 0) {
                badge.classList.remove('bg-success');
                badge.classList.add('bg-danger');
            } else {
                badge.classList.remove('bg-danger');
                badge.classList.add('bg-success');
            }
        });

        const variantInput = document.querySelector(`input[name="selected_variant"][data-code="${vCode}"]`);

        if (variantInput) {
            const container = variantInput.closest('.variant-option');
            const stockDisplay = container.querySelector('strong');

            if (stockDisplay) {
                stockDisplay.innerText = newStock;

                if (newStock <= 0) {
                    variantInput.disabled = true;
                    variantInput.checked = false;
                    stockDisplay.className = 'text-danger';
                    stockDisplay.innerText = 'Habis';

                    const qtyGroup = container.querySelector('.qty-input-group');
                    if (qtyGroup) qtyGroup.style.display = 'none';
                } else {
                    variantInput.disabled = false;
                    stockDisplay.className = 'text-success';

                    const qtyInput = container.querySelector('input[type="number"]');
                    if (qtyInput) {
                        qtyInput.max = newStock;
                        if (parseInt(qtyInput.value) > newStock) {
                            qtyInput.value = newStock;
                        }
                    }
                }
            }
        }
    });
</script>

window._ = require('lodash');

/**
 * Load Axios
 */
window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

let token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

/**
 * Laravel Echo & Reverb Setup
 * Menggunakan kredensial Reverb secara langsung.
 */
import Echo from 'laravel-echo';
window.Pusher = require('pusher-js');

if (typeof APP !== 'undefined') {
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: APP.REVERB_APP_KEY,
        wsHost: 'slcms.test',
        wsPort: 8080, // Tembak langsung ke port Reverb
        wssPort: 8080,
        forceTLS: true, // Tetap pakai WSS
        enabledTransports: ['ws', 'wss'],
    });

    console.info('Echo (Reverb) initialized on: ' + (APP.REVERB_HOST || window.location.hostname));

    window.Echo.connector.pusher.connection.bind('connected', () => {
        console.log('Connected to Reverb via WSS (Secure)!');
    });

    /**
     * Listener Real-time Stok
     */
    window.Echo.channel('product-stock').listen('ProductStockUpdated', (e) => {
        console.log('Update stok diterima:', e);

        let stockLabel = document.querySelector(`.stock-display[data-product-id="${e.productId}"]`);
        if (stockLabel && !e.variantCode) {
            stockLabel.innerText = e.newStock;
        }

        if (e.variantCode) {
            let variantLabels = document.querySelectorAll(
                `.variant-stock[data-code="${e.variantCode}"]`
            );
            variantLabels.forEach((label) => {
                label.innerText = e.newStock;
            });
        }
    });
}

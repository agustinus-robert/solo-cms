<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
<script src="https://cdn.jsdelivr.net/npm/pusher-js@8.3.0/dist/web/pusher.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    console.log('🚀 INIT ECHO START');

    window.Pusher = Pusher;
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: 'slcmskey',
        wsHost: window.location.hostname,
        wsPort: 8080, // PAKAI 8080
        wssPort: 8080, // PAKAI 8080
        forceTLS: true, // Matikan dulu TLS buat debug
        enabledTransports: ['ws', 'wss'],
        disableStats: true,
    });

    console.log('ECHO INSTANCE CREATED');

    const pusher = window.Echo.connector.pusher;

    pusher.connection.bind('state_change', (states) => {
        console.log('🔄 STATE:', states.previous, '➡️', states.current);
    });

    pusher.connection.bind('connecting', () => {
        console.log('⏳ CONNECTING...');
    });

    pusher.connection.bind('connected', () => {
        console.log('✅ CONNECTED (WSS SUCCESS)');
        console.log('📡 SOCKET ID:', pusher.connection.socket_id);
    });

    pusher.connection.bind('disconnected', () => {
        console.warn('⚠️ DISCONNECTED');
    });

    pusher.connection.bind('unavailable', () => {
        console.error('🚫 UNAVAILABLE (SERVER DOWN / SSL SALAH)');
    });

    pusher.connection.bind('failed', () => {
        console.error('💀 FAILED (HANDSHAKE GAGAL)');
    });

    pusher.connection.bind('error', (err) => {
        console.error('🔥 ERROR DETAIL:', err);
    });


    setTimeout(() => {
        try {
            const transport = pusher.connection.transport?.name;
            console.log('🚚 TRANSPORT:', transport);

            if (transport !== 'ws' && transport !== 'wss') {
                console.error('❌ BUKAN WEBSOCKET!');
            } else {
                console.log('✅ WEBSOCKET AKTIF:', transport);
            }
        } catch (e) {
            console.error('❌ TRANSPORT CHECK FAILED', e);
        }
    }, 2000);

    const testUrl = `wss://${window.location.hostname}/app/slcmskey?protocol=7&client=js&version=8.3.0&flash=false`;
    console.log('🧪 TEST DIRECT WSS:', testUrl);

    try {
        const ws = new WebSocket(testUrl);

        ws.onopen = () => console.log('🟢 RAW WSS CONNECTED');
        ws.onerror = (e) => console.error('🔴 RAW WSS ERROR', e);
        ws.onclose = (e) => console.warn('🟡 RAW WSS CLOSED', e);
    } catch (e) {
        console.error('❌ RAW WSS FAILED', e);
    }

    // window.Echo.channel('products-market')
    //     .listen('.stock.updated', (data) => {
    //         console.log("📦 EVENT RECEIVED:", data);
    //     });


});
</script>

@auth
    @include('partials.admin-notif-toast')
@endauth

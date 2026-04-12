# 🚀 Laravel Realtime Setup (Reverb + Nginx + Herd)

Dokumentasi ini menjelaskan cara menjalankan fitur realtime pada Laravel menggunakan **Reverb WebSocket Server** dengan **Herd (Valet)** dan **Nginx (SSL / WSS)**.

## jangan lupa untuk herd **secure** dan **import sertifikat** di firefox

# 📦 Requirement

- PHP 8.2+
- Laravel 10/11+
- Herd (Valet for Mac)
- Nginx (auto dari Herd)
- SSL lokal (`*.test` dari Herd)

---

# ⚙️ 1. Install Reverb

```bash
composer require laravel/reverb
php artisan reverb:install
```

---

# ⚙️ 2. Konfigurasi `.env`

```env
BROADCAST_DRIVER=reverb
BROADCAST_CONNECTION=reverb

REVERB_APP_ID=slcms-app
REVERB_APP_KEY=slcmskey
REVERB_APP_SECRET=slcmssecret

REVERB_HOST=127.0.0.1
REVERB_PORT=8080
REVERB_SCHEME=http

REVERB_SERVER_HOST=127.0.0.1
REVERB_SERVER_PORT=8080

# Frontend (Vite / Echo)
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="slcms.test"
VITE_REVERB_PORT=443
VITE_REVERB_SCHEME=https
```

---

# ⚙️ 3. Konfigurasi Reverb (`config/reverb.php`)

Gunakan TLS dari Herd:

```php
'tls' => [
    'local_cert' => '/Users/robert/Library/Application Support/Herd/config/valet/Certificates/slcms.test.crt',
    'local_pk'   => '/Users/robert/Library/Application Support/Herd/config/valet/Certificates/slcms.test.key',
    'verify_peer' => false,
],
```

---

# ⚙️ 4. Konfigurasi Nginx (Herd)

Edit file:

```
~/Library/Application Support/Herd/config/valet/Nginx/slcms.test
```

Tambahkan config WebSocket:

```nginx
location /app/ {
    proxy_pass https://0.0.0.0:8080$request_uri;

    proxy_http_version 1.1;

    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection "Upgrade";

    proxy_set_header Host $host;
    proxy_set_header X-Forwarded-For $remote_addr;
    proxy_set_header X-Forwarded-Proto $scheme;

    proxy_ssl_verify off;

    proxy_read_timeout 60s;
    proxy_send_timeout 60s;
}
```

---

# ⚙️ 5. Jalankan Reverb Server

```bash
php artisan reverb:start --host=0.0.0.0 --port=8080 --debug
```

Output normal:

```
Starting secure server on 0.0.0.0:8080
```

---

# ⚙️ 6. Setup Laravel Echo (Frontend)

```js
import Echo from 'laravel-echo';

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: 443,
    wssPort: 443,
    forceTLS: true,
});
```

---

# 🧪 7. Test Connection

Buka browser → DevTools → Network → WS

Harus muncul:

```
101 Switching Protocols
CONNECTED (WSS SUCCESS)
```

---

# 📡 8. Test Event

## Event Laravel

```php
broadcast(new TestEvent('Hello Realtime'));
```

## Listen di Frontend

```js
Echo.channel('test').listen('TestEvent', (e) => {
    console.log(e);
});
```

---

# 🔥 Troubleshooting

## ❌ 502 Bad Gateway

- Salah `proxy_pass`
- Reverb belum jalan
- Port 8080 tidak listen

## ❌ WebSocket tidak connect

- Pastikan pakai **https + wss**
- Cek `VITE_REVERB_HOST`
- Jangan pakai `localhost`, gunakan domain `.test`

## ❌ Connect tapi event tidak masuk

- Cek `BROADCAST_DRIVER`
- Cek queue (`sync` vs `redis`)
- Cek channel name

---

# 🧠 Arsitektur

```
Browser (WSS)
    ↓
Nginx (SSL terminate)
    ↓
Reverb Server (127.0.0.1:8080)
    ↓
Laravel Broadcast
```

---

# ✅ Status

- ✅ WebSocket (WSS) aktif
- ✅ SSL via Herd
- ✅ Reverb terhubung
- ✅ Siap realtime (chat, notif, dll)

---

# 🚀 Next Step

- Private Channel (auth)
- Presence Channel (online user)
- Redis scaling
- Queue async

---

# 👨‍💻 Author

Robert Steven
Laravel Realtime Setup with Reverb + Herd + Nginx

---

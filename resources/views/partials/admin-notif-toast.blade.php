@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    /**
     * Pastikan ID User login tersedia.
     * Jika kamu pakai Blade, ambil langsung dari auth()->id().
     */
    const userId = "{{ auth()->id() }}";

    const waitEcho = setInterval(() => {
        if (!window.Echo) return;
        clearInterval(waitEcho);

        // SESUAIKAN: Menggunakan .private() dan namespace model User kamu
        window.Echo.private(`Modules.Account.Models.User.${userId}`)
            .listen('.notification.received', (data) => {
                showFbNotification(data);
            });

    }, 100);
});

// =========================
// NOTIF STACK MANAGER
// =========================
const MAX_NOTIF = 5;

function getContainer() {
    let container = document.getElementById('fb-notif-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'fb-notif-container';
        container.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: flex;
            flex-direction: column-reverse;
            gap: 12px;
            z-index: 2147483647;
            width: 350px;
            pointer-events: none;
        `;
        document.body.appendChild(container);
    }
    return container;
}

function showFbNotification(data) {
    const container = getContainer();
    const notif = document.createElement('div');

    const colors = {
        primary: '#1877f2',
        success: '#42b72a',
        warning: '#f7b928',
        danger: '#fa3e3e'
    };
    const activeColor = colors[data.color] || colors.primary;

    notif.className = 'fb-toast';
    notif.style.cssText = `
        background: #ffffff;
        border-radius: 8px;
        padding: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        font-family: 'Segoe UI', Helvetica, Arial, sans-serif;
        position: relative;
        opacity: 0;
        transform: translateX(120%);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        cursor: pointer;
        pointer-events: auto;
        display: flex;
        align-items: flex-start; /* GAMBAR TETEP DI ATAS KIRI */
        gap: 12px;
        margin-bottom: 10px;
        width: 100%;
        border: 1px solid #ddd;
    `;

    notif.innerHTML = `
        <div style="flex-shrink: 0;">
            <div style="position: relative;">
                <img src="${data.sender_image}" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 1px solid #eee;">
                <div style="
                    position: absolute;
                    bottom: -2px;
                    right: -2px;
                    background: ${activeColor};
                    width: 20px;
                    height: 20px;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    border: 2px solid #fff;
                    color: #fff;
                    font-size: 10px;
                ">
                    <i class="${data.icon || 'bx bx-bell'}"></i>
                </div>
            </div>
        </div>

        <div style="flex: 1; display: flex; flex-direction: column;">
            <div style="font-weight: 700; font-size: 14px; color: #050505; margin-bottom: 2px;">
                ${data.sender_name}
            </div>

            <div style="font-size: 13px; color: #65676b; line-height: 1.2; margin-bottom: 4px;">
                ${data.action}
            </div>

            <div style="font-size: 13px; color: #050505; font-weight: 500; line-height: 1.3;">
                ${data.message}
            </div>
        </div>

        <button style="
            position: absolute;
            top: 8px;
            right: 8px;
            border: none;
            background: transparent;
            color: #999;
            font-size: 18px;
            cursor: pointer;
            line-height: 1;
        ">×</button>
    `;

    notif.onclick = () => {
        if (data.link && data.link !== '#') window.location.href = data.link;
    };

    notif.querySelector('button').onclick = (e) => {
        e.stopPropagation();
        removeNotif(notif);
    };

    container.prepend(notif);
    requestAnimationFrame(() => {
        notif.style.transform = 'translateX(0)';
        notif.style.opacity = '1';
    });

    setTimeout(() => removeNotif(notif), 8000);
    enforceLimit(container);
}

function removeNotif(el) {
    if (!el) return;
    el.style.transform = 'translateX(120%)';
    el.style.opacity = '0';
    setTimeout(() => el.remove(), 400);
}

function enforceLimit(container) {
    const items = container.querySelectorAll('.fb-toast');
    if (items.length > MAX_NOTIF) {
        // Hapus yang paling lama (paling bawah) jika melebihi limit
        for (let i = MAX_NOTIF; i < items.length; i++) {
            removeNotif(items[i]);
        }
    }
}
</script>
@endpush

@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', () => {

    const waitEcho = setInterval(() => {
        if (!window.Echo) return;
        clearInterval(waitEcho);

        window.Echo.channel('test-channel')
            .listen('.test.event', (data) => {
                showFbNotification(data.message);
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
            gap: 10px;
            z-index: 2147483647;
            width: 320px;
        `;

        document.body.appendChild(container);
    }

    return container;
}

function showFbNotification(message) {

    const container = getContainer();

    const notif = document.createElement('div');

    notif.className = 'fb-toast';

    notif.style.cssText = `
        background: #fff;
        border-left: 5px solid #1877f2;
        padding: 12px 14px;
        box-shadow: 0 6px 18px rgba(0,0,0,0.25);
        border-radius: 10px;
        font-family: Arial;
        font-size: 14px;
        color: #333;
        position: relative;
        opacity: 0;
        transform: translateX(120%);
        transition: all .25s ease;
        cursor: pointer;
    `;

    notif.innerHTML = `
        <div style="font-weight:bold;margin-bottom:4px;">Test Channel</div>
        <div>${message}</div>

        <button style="
            position:absolute;
            top:6px;
            right:8px;
            border:none;
            background:transparent;
            font-size:16px;
            cursor:pointer;
        ">×</button>
    `;

    // close button
    notif.querySelector('button').onclick = (e) => {
        e.stopPropagation();
        removeNotif(notif);
    };

    container.prepend(notif);

    // animate in
    requestAnimationFrame(() => {
        notif.style.transform = 'translateX(0)';
        notif.style.opacity = '1';
    });

    // auto remove
    setTimeout(() => removeNotif(notif), 6000);

    // LIMIT 5 NOTIF
    enforceLimit(container);
}

// =========================
// REMOVE ANIMATION
// =========================
function removeNotif(el) {
    if (!el) return;

    el.style.transform = 'translateX(120%)';
    el.style.opacity = '0';

    setTimeout(() => el.remove(), 250);
}

// =========================
// MAX 5 STACK CONTROL
// =========================
function enforceLimit(container) {

    const items = container.querySelectorAll('.fb-toast');

    if (items.length > MAX_NOTIF) {
        const toRemove = items.length - MAX_NOTIF;

        for (let i = 0; i < toRemove; i++) {
            removeNotif(items[items.length - 1 - i]);
        }
    }
}
</script>

@endpush

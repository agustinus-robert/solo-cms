<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>


document.addEventListener('DOMContentLoaded', () => {
    const checkEcho = setInterval(() => {
        if (window.Echo) {
            clearInterval(checkEcho);

            const userId = "{{ auth()->id() }}";

            window.Echo.private(`Modules.Account.Models.User.${userId}`)
                .notification((notification) => {
                    console.log('🔔 NOTIF RECEIVED:', notification);

                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'bottom-end',
                        showConfirmButton: true,
                        confirmButtonText: 'Buka',
                        showCloseButton: true,
                        timer: 10000,
                        timerProgressBar: true,
                        background: '#ffffff',
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    });

                    Toast.fire({
                        html: `
                            <div style="display: flex; align-items: center; text-align: left;">
                                <div style="font-size: 24px; margin-right: 15px;" class="${notification.color === 'success' ? 'text-success' : 'text-warning'}">
                                    <i class="${notification.icon}"></i>
                                </div>
                                <div>
                                    <strong style="display: block;">${notification.title}</strong>
                                    <small style="color: #666;">${notification.message}</small>
                                </div>
                            </div>
                        `,
                    }).then((result) => {
                        if (result.isConfirmed && notification.link && notification.link !== '#') {
                            window.location.href = notification.link;
                        }
                    });
                });
        }
    }, 100);
});
</script>

<style>
    .swal2-toast {
        padding: 15px !important;
        border-left: 5px solid #2c3e50; /* Warna aksen gelap */
        box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
    }
    .swal2-html-container {
        margin: 0 !important;
        padding: 0 !important;
    }
</style>

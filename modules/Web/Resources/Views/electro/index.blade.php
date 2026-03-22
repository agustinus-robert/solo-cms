<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>@yield('title') - Electronics Website</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <link href="{{ asset('themes/electro/lib/animate/animate.min.css') }}" rel="stylesheet">
    <link href="{{ asset('themes/electro/lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">

    <link href="{{ asset('themes/electro/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('themes/electro/css/style.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    @if($canEdit)
        <style>
            body { overflow: hidden; margin: 0; padding: 0; width: 100vw; height: 100vh; }
            .builder-layout { display: flex; width: 100%; height: 100vh; overflow: hidden; background-color: #e2e8f0; }
            #customizer-sidebar { width: 450px; min-width: 450px; flex-shrink: 0; background-color: white; z-index: 1030; position: relative; overflow-y: auto; }
            .sidebar-closed { margin-left: -450px !important; opacity: 0; visibility: hidden; }
            .main-content-area { flex-grow: 1; display: flex; flex-direction: column; min-width: 0; transition: all 0.3s ease; height: 100vh; }
            .builder-topbar { height: 65px; background: white; display: flex; align-items: center; justify-content: center; border-bottom: 1px solid #dee2e6; flex-shrink: 0; }

            #preview-container {
                flex: 1;
                padding: 20px;
                overflow-y: auto !important;
                overflow-x: hidden;
                background-color: #e2e8f0;
                display: block;
            }
            #preview-frame {
                width: 100%;
                min-height: 100%;
                height: auto !important;
                background: white;
                border-radius: 16px;
            }

            /* PAKSA SEMUA SECTION MUNCUL (Matikan efek WOW.js di mode edit) */
            .wow {
                visibility: visible !important;
                animation-name: none !important;
                opacity: 1 !important;
            }
        </style>
    @endif
</head>

<body>
    @php
        function initTheme(){
            echo view('web::electro.global.spinner')->render();
            echo view('web::electro.global.topbar')->render();
            echo view('web::electro.global.navbar')->render();
        }
    @endphp

    @if($canEdit)
        <div class="builder-layout">

            <aside id="customizer-sidebar" class="border-end shadow-sm">
                <div style="width: 450px;"> <div class="p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary p-2 rounded-3 me-3">
                                    <i class="fas fa-paint-brush text-white"></i>
                                </div>
                                <h5 class="m-0 fw-bold text-dark">Customizer</h5>
                            </div>
                            <button type="button" class="btn btn-sm btn-light border" onclick="toggleSidebar()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <hr class="my-3">

                        <div id="customizer-content">
                            <div class="card border-dashed bg-light py-5">
                                <div class="card-body text-center">
                                    <i class="fas fa-mouse-pointer fa-2x text-muted mb-3"></i>
                                    <p class="text-muted small mb-0 px-4">
                                        Klik elemen di sisi kanan untuk mulai mengedit konten secara live.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            <div class="main-content-area">
                <div class="builder-topbar shadow-sm">
                    <button id="btn-sidebar-open" class="btn btn-primary btn-sm d-none" style="position: absolute; left: 20px;" onclick="toggleSidebar()">
                        <i class="fas fa-bars me-2"></i> Buka Sidebar
                    </button>

                    <div class="fw-bold text-dark text-center">
                        Anda sedang berada di page <span class="text-primary">{{ $pages ?? 'Home' }}</span>
                    </div>

                    <div style="position: absolute; right: 20px;">
                        <a href="{{ url()->current() }}" class="btn btn-outline-danger btn-sm px-3 rounded-pill">
                            <i class="fas fa-sign-out-alt me-2"></i> Keluar
                        </a>
                    </div>
                </div>

                <div id="preview-container">
                    <div id="preview-frame" class="card shadow-lg border-0">
                        <div class="bg-white" style="width: 100%; overflow-x: hidden;">
                            <main class="animate__animated animate__fadeIn">
                                <div id="page-preview">
                                    {!! initTheme() !!}
                                    @yield('content')
                                    @include('web::electro.footer')
                                </div>
                            </main>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        {!! initTheme() !!}
        @yield('content')
        @include('web::electro.footer')
    @endif

    <a href="#" class="btn btn-primary btn-lg-square back-to-top"><i class="fa fa-arrow-up"></i></a>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('themes/electro/lib/wow/wow.min.js')  }}"></script>
    <script src="{{ asset('themes/electro/lib/owlcarousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('themes/electro/js/main.js')  }}"></script>

    @if($canEdit)
        <script src="https://unpkg.com/htmx.org@1.9.10"></script>
        <script>
            function toggleSidebar() {
                const sidebar = document.getElementById('customizer-sidebar');
                const btnOpen = document.getElementById('btn-sidebar-open');

                if (!sidebar) return;

                sidebar.classList.toggle('sidebar-closed');

                if (sidebar.classList.contains('sidebar-closed')) {
                    btnOpen.classList.remove('d-none');
                } else {
                    btnOpen.classList.add('d-none');
                }

                setTimeout(() => {
                    window.dispatchEvent(new Event('resize'));
                }, 300);
            }
        </script>
    @endif

    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
    @include('web::electro.global.echos')

    @stack('scripts')
</body>
</html>

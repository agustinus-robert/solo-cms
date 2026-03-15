<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>@yield('title') - Electronics Website</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&family=Roboto:wght@400;500;700&display=swap"
        rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="{{ asset('themes/electro/lib/animate/animate.min.css') }}" rel="stylesheet">
    <link href="{{ asset('themes/electro/lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">


    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{ asset('themes/electro/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="{{ asset('themes/electro/css/style.css') }}" rel="stylesheet">
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
        <div class="d-flex" style="min-height: 100vh; overflow: hidden; background-color: #e2e8f0;">
            <aside id="customizer-sidebar"
                class="bg-white border-end shadow-sm d-none d-lg-block"
                style="width: 450px; min-width: 450px; flex-shrink: 0; position: sticky; top: 0; height: 100vh; z-index: 1030; overflow-y: auto; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);">

                <div class="p-4">
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

                    <hr class="my-3 opacity-75">

                    <div id="customizer-content">
                        <div class="card border-dashed bg-light py-5">
                            <div class="card-body text-center">
                                <i class="fas fa-mouse-pointer fa-2x text-muted mb-3"></i>
                                <p class="text-muted small mb-0 px-4">Klik elemen di sisi kanan untuk mulai mengedit konten secara live.</p>
                            </div>
                        </div>
                        </div>
                </div>
            </aside>

            <div class="flex-grow-1 d-flex flex-column" style="min-width: 0;">

                <div class="d-flex justify-content-center align-items-center bg-white py-2 border-bottom shadow-sm sticky-top"
                    style="z-index: 1025; height: 65px; position: relative;">

                    <button id="btn-sidebar-open" class="btn btn-primary btn-sm d-none" style="position: absolute; left: 20px;" onclick="toggleSidebar()">
                        <i class="fas fa-bars me-2"></i> Buka Sidebar
                    </button>

                    <div class="fw-bold text-dark text-center">
                        Anda sedang berada di page <span class="text-primary">{{ $pages ?? 'Home' }}</span>
                    </div>

                    <div style="position: absolute; right: 20px;">
                        <a href="{{ url()->current() }}" class="btn btn-outline-danger btn-sm px-3 rounded-pill">
                            <i class="fas fa-sign-out-alt me-2"></i> Keluar dari Live Editor
                        </a>
                    </div>
                </div>

                <div id="preview-container" class="flex-grow-1 p-3 p-lg-4 d-flex justify-content-center overflow-auto" style="height: calc(100vh - 65px);">
                    <div id="preview-frame" class="card shadow-lg border-0 flex-grow-1 overflow-hidden rounded-4 shadow-2xl"
                        style="max-width: 100%; transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1); height: fit-content; min-height: 100%; background: #fff;">

                        <div class="flex-grow-1 bg-white">
                            <main class="animate__animated animate__fadeIn animate__faster">
                                <div id="page-preview">
                                    {!! initTheme() !!}
                                    @yield('content')
                                </div>
                            </main>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function toggleSidebar() {
                const sidebar = document.getElementById('customizer-sidebar');
                const btnOpen = document.getElementById('btn-sidebar-open');

                if (sidebar.style.marginLeft === '-450px') {
                    sidebar.style.marginLeft = '0';
                    btnOpen.classList.add('d-none');
                } else {
                    sidebar.style.marginLeft = '-450px';
                    btnOpen.classList.remove('d-none');
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                const sidebar = document.getElementById('customizer-sidebar');
                setTimeout(() => {
                    sidebar.style.transition = 'margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
                }, 100);
            });
        </script>

        <style>
            .border-dashed {
                border: 2px dashed #dee2e6 !important;
            }

            body {
                overflow: hidden;
            }

            #customizer-sidebar::-webkit-scrollbar {
                width: 5px;
            }

            #customizer-sidebar::-webkit-scrollbar-thumb {
                background: #ccc;
                border-radius: 10px;
            }
        </style>
    @else
        {!! initTheme() !!}
        @yield('content')
    @endif

    <a href="#" class="btn btn-primary btn-lg-square back-to-top"><i class="fa fa-arrow-up"></i></a>


    <!-- JavaScript Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('themes/electro/lib/wow/wow.min.js')  }}"></script>
    <script src="{{ asset('themes/electro/lib/owlcarousel/owl.carousel.min.js') }}"></script>


    <!-- Template Javascript -->
    <script src="{{ asset('themes/electro/js/main.js')  }}"></script>
</body>

</html>

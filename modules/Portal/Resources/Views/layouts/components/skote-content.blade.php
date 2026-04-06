<div class="page-content">
    <div class="container-fluid">
        {{-- Alert Messages --}}
        @if (Session::has('msg-sukses'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 2000)" x-show="show" class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="mdi mdi-check-all me-2"></i> {{ Session::get('msg-sukses') }}
                <button type="button" class="btn-close" @click="show = false"></button>
            </div>
        @endif

        @if (Session::has('msg-gagal'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 2000)" x-show="show" class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="mdi mdi-block-helper me-2"></i> {{ Session::get('msg-gagal') }}
                <button type="button" class="btn-close" @click="show = false"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12 text-between d-flex align-items-center justify-content-between mb-3">
                <h4 class="mb-0 font-size-18">Dashboard MSDM</h4>
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item small"><a href="javascript: void(0);">Portal</a></li>
                    <li class="breadcrumb-item active small">Dashboard</li>
                </ol>
            </div>
        </div>

        {{-- ROW UTAMA DENGAN D-FLEX UNTUK MENYAMAKAN TINGGI --}}
        <div class="row d-flex align-items-stretch">

            {{-- KOLOM KIRI --}}
            <div class="col-xl-4 d-flex flex-column">
                {{-- Profile --}}
                <div class="card overflow-hidden">
                    <div class="bg-primary-subtle">
                        <div class="row">
                            <div class="col-7"><div class="text-primary p-3"><h5 class="text-primary">Selamat Datang!</h5><p class="mb-0 small">Solo CMS</p></div></div>
                            <div class="col-5 align-self-end"><img src="{{ asset('skote/images/profile-img.png') }}" class="img-fluid"></div>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="row">
                            <div class="col-sm-5">
                                <div class="avatar-md profile-user-wid mb-2">
                                    <img src="{{ asset('skote/images/users/avatar-1.jpg') }}" class="img-thumbnail rounded-circle">
                                </div>
                                <h5 class="font-size-14 text-truncate mb-1">{{ $user->name }}</h5>
                            </div>
                            <div class="col-sm-7 pt-3">
                                <a href="{{ route('account::manage-profile.index') }}" class="btn btn-primary btn-sm w-100">Profil <i class="mdi mdi-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card Izin & Cuti (Menggunakan flex-grow agar mengisi sisa ruang secara merata) --}}
                <div class="card flex-grow-1 mb-3">
                    <div class="card-body d-flex flex-column">
                        <h4 class="card-title mb-3">Daftar Perizinan</h4>
                        <div class="scrollable-container flex-grow-1" style="max-height: 180px; overflow-y: auto;">
                            <ul class="verti-timeline list-unstyled">
                                @forelse($leaves_today as $leave)
                                    <li class="event-list">
                                        <div class="event-timeline-dot"><i class="bx bx-right-arrow-circle text-primary"></i></div>
                                        <div class="flex-grow-1">
                                            <h5 class="font-size-13 mb-0">{{ $leave->employee->user->name }}</h5>
                                            <p class="text-muted mb-0 small">{{ $leave->category->name }}</p>
                                        </div>
                                    </li>
                                @empty
                                    <li class="text-center text-muted py-4 small">Kosong</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="card flex-grow-1 mb-xl-0">
                    <div class="card-body d-flex flex-column">
                        <h4 class="card-title mb-3">Daftar Cuti</h4>
                        <div class="scrollable-container flex-grow-1" style="max-height: 180px; overflow-y: auto;">
                            <ul class="verti-timeline list-unstyled">
                                @forelse($vacations_today->filter(fn ($vacation) => empty($vacation->dates[0]['cashable'] ?? null)) as $vacation)
                                    <li class="event-list">
                                        <div class="event-timeline-dot"><i class="bx bx-right-arrow-circle text-success"></i></div>
                                        <div class="flex-grow-1">
                                            <h5 class="font-size-13 mb-0">{{ $vacation->quota->employee->user->name }}</h5>
                                            <p class="text-muted mb-0 small">{{ $vacation->quota->category->name }}</p>
                                        </div>
                                    </li>
                                @empty
                                    <li class="text-center text-muted py-4 small">Kosong</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN --}}
            <div class="col-xl-8 d-flex flex-column">
                {{-- Stats --}}
                <div class="row">
                    <div class="col-md-4">
                        <div class="card mini-stats-wid">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1"><p class="text-muted small fw-medium">Izin</p><h4 class="mb-0">{{ $leaves_today->count() }}</h4></div>
                                    <div class="mini-stat-icon avatar-xs align-self-center rounded-circle bg-primary"><span class="avatar-title"><i class="bx bx-copy-alt"></i></span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card mini-stats-wid">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1"><p class="text-muted small fw-medium">Cuti</p><h4 class="mb-0">{{ $vacations_today->count() }}</h4></div>
                                    <div class="mini-stat-icon avatar-xs align-self-center rounded-circle bg-info"><span class="avatar-title"><i class="bx bx-archive-in"></i></span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card mini-stats-wid">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1"><p class="text-muted small fw-medium">Presensi</p><a class="btn btn-soft-primary btn-sm rounded-pill" href="{{ route('portal::attendance.presence.index', ['type' => 'WFO', 'position' => 'employee']) }}">Absen</a></div>
                                    <div class="mini-stat-icon avatar-xs align-self-center rounded-circle bg-success"><span class="avatar-title"><i class="mdi mdi-fingerprint"></i></span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Chart --}}
                <div class="card flex-grow-1">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Statistik Kehadiran</h4>
                        <canvas id="pie" style="max-height: 200px; width: 100%;"></canvas>
                    </div>
                </div>

                {{-- Menu & Kegiatan --}}
                <div class="row flex-grow-1">
                    <div class="col-md-6 d-flex">
                        <div class="card w-100 mb-0">
                            <div class="card-body">
                                <h4 class="card-title mb-4">Menu Pengelolaan</h4>
                                <div class="row g-2 text-center">
                                    @php
                                        $menus = [
                                            ['route' => 'portal::leave.submission.index', 'icon' => 'mdi-account-group', 'color' => 'bg-info', 'label' => 'Izin'],
                                            ['route' => 'portal::overtime.submission.index', 'icon' => 'mdi-clock-time-five-outline', 'color' => 'bg-warning', 'label' => 'Lembur'],
                                            ['route' => 'portal::vacation.submission.index', 'icon' => 'mdi-beach', 'color' => 'bg-danger', 'label' => 'Cuti'],
                                            ['route' => 'portal::outwork.submission.index', 'icon' => 'mdi-calendar-check', 'color' => 'bg-secondary', 'label' => 'Kegiatan'],
                                        ];
                                    @endphp
                                    @foreach($menus as $menu)
                                        <div class="col-6">
                                            <a href="{{ route($menu['route']) }}" class="d-block py-2 rounded bg-light-subtle hover-shadow transition border">
                                                <div class="avatar-xs mx-auto mb-1">
                                                    <span class="avatar-title rounded-circle {{ $menu['color'] }} font-size-14"><i class="mdi {{ $menu['icon'] }}"></i></span>
                                                </div>
                                                <span class="font-size-12 text-dark">{{ $menu['label'] }}</span>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 d-flex">
                        <div class="card w-100 mb-0">
                            <div class="card-body">
                                <h4 class="card-title mb-4">Kegiatan Terbaru</h4>
                                <div class="text-center pt-4">
                                    <i class="mdi mdi-calendar-blank mdi-24px text-light"></i>
                                    <p class="text-muted small">Belum ada data.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Paksa kolom memiliki tinggi yang sama di layar desktop */
    @media (min-width: 1200px) {
        .row.d-flex.align-items-stretch { display: flex; }
        .col-xl-4, .col-xl-8 { display: flex; flex-direction: column; }
        .card { margin-bottom: 1.25rem; } /* Jarak antar card */
    }

    .scrollable-container::-webkit-scrollbar { width: 4px; }
    .scrollable-container::-webkit-scrollbar-thumb { background: #e2e2e2; border-radius: 10px; }

    .hover-shadow:hover {
        background-color: #fff !important;
        box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        transform: translateY(-2px);
        border-color: #556ee6 !important;
    }
    .transition { transition: all 0.2s ease-in-out; }
    .event-list { padding-bottom: 10px !important; margin-bottom: 10px; border-bottom: 1px solid #f8f8f8; }
    .event-list:last-child { border-bottom: none; }
</style>

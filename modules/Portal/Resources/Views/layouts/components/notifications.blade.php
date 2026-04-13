@php
    $authUser = auth()->user();
    $unreadCount = $authUser ? $authUser->notifications()->whereNull('read_at')->count() : 0;
@endphp

<div class="dropdown ms-1">
    <button type="button" class="btn header-item noti-icon waves-effect" id="page-header-notifications-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="bx bx-bell bx-tada"></i>
        <span id="notif-count-data" class="badge bg-danger rounded-pill {{ $unreadCount == 0 ? 'd-none' : '' }}">
            {{ $unreadCount }}
        </span>
    </button>

    <div id="nav-dropdown-notifications" class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0 shadow-sm border-0" style="min-width: 350px">
        <div class="p-3">
            <div class="row align-items-center">
                <div class="col">
                    <h6 class="m-0 font-size-16" key="t-notifications"> Notifications </h6>
                </div>
                <div id="mark-all-read-container" class="col-auto">
                    @if ($unreadCount > 0)
                        <a href="{{ route('account::notifications.read-all', ['next' => url()->full()]) }}" class="small text-primary">
                            Tandai semua dibaca
                        </a>
                     @endif
                </div>
            </div>
        </div>

        <div data-simplebar style="max-height: 230px;" id="notification-list">
            @if($authUser)
                @forelse($authUser->notifications->take(4) as $notification)
                    <a class="dropdown-item d-flex align-items-center {{ !$notification->read_at ? 'bg-light' : '' }} py-3"
                       href="{{ isset($notification->data['link']) ? route('account::notifications.read', ['id' => $notification->id, 'next' => $notification->data['link'] ?? url()->full()]) : 'javascript:;' }}">

                        <div class="flex-shrink-0 me-3">
                            <div class="avatar-xs">
                                <span class="avatar-title bg-{{ $notification->data['color'] ?? 'primary' }} rounded-circle font-size-16">
                                    <i class="{{ $notification->data['icon'] ?? 'bx bx-bell' }}"></i>
                                </span>
                            </div>
                        </div>

                        <div class="flex-grow-1">
                            <div class="text-wrap font-size-13 text-muted">
                                {!! Str::words($notification->data['message'] ?? '-', 8) !!}
                            </div>
                            <p class="mb-0 small text-muted">
                                <i class="mdi mdi-clock-outline"></i> {{ optional($notification->created_at)->diffForHumans() }}
                            </p>
                        </div>

                        @if (!$notification->read_at)
                            <div class="flex-shrink-0 ms-2">
                                <span class="badge bg-danger rounded-circle p-1" style="width: 6px; height: 6px; display: inline-block;"></span>
                            </div>
                        @endif
                    </a>
                @empty
                    <div class="dropdown-item py-4 text-center text-muted">
                        <i class="bx bx-notification-off font-size-24 d-block mb-2"></i>
                        Tidak ada notifikasi
                    </div>
                @endforelse
            @endif
        </div>

        <div class="p-2 border-top d-grid">
            <a class="btn btn-sm btn-link font-size-14 text-center" href="{{ route('account::notifications', ['next' => url()->full()]) }}">
                <i class="mdi mdi-arrow-right-circle me-1"></i> <span key="t-view-more">Lihat Semua..</span>
            </a>
        </div>
    </div>
</div>

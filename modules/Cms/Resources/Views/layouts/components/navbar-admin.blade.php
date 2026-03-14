@if (!isset($_COOKIE['k_language']) || $_COOKIE['k_language'] == 'undefined')
    @php
        setcookie('k_language', 'id', time() + (86400 * 30), '/');
    @endphp
@endif

<div class="sidebar bg-dark open border-end text-white" style="z-index: 9999;">
    <div class="sidebar-header">
        <a class="d-flex align-items-center justify-content-center border-bottom text-center text-white" style="height: 80px;" href="{{ route('account::home') }}">
            <img height="24" src="{{ asset('img/logo/logo-icon-bw-32.png') }}" alt="">
            <div class="h5 mb-0 ps-2">P<span class="text-danger">é</span>Mad</div>
        </a>
    </div>

    <div class="sidebar-body">
        <div class="sidebar-body-menu">
            <ul class="nav flex-column">
                <li class="divider">Utama</li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('cms::dashboard') }}">
                        <i class="mdi mdi-apps me-2"></i> Dasbor
                    </a>
                </li>

                @if (env('BUG') == 1)
                    @php
                        $kount_menu = json_decode(get_menu_order(), false);
                    @endphp

                    @foreach ($kount_menu as $menu)
                        @php
                            $needed = get_needed($menu->id);
                            $menuTitle = json_decode($needed[0]->title ?? '{}', true)[@$_COOKIE['k_language']] ?? 'Tanpa Judul';
                            $menuIcon = $needed[0]->icon ?? 'mdi mdi-circle';
                        @endphp

                        @if (isset($menu->children) && isset($needed[0]->type))
                            <li class="nav-item has-submenu pt-3">
                                <a class="nav-link" href="#">
                                    <i class="{{ $menuIcon }} me-2"></i> {{ $menuTitle }}
                                </a>
                                <ul class="submenu collapse">
                                    @foreach ($menu->children as $child)
                                        @php
                                            $childData = get_needed($child->id);
                                            $childType = $childData[0]->type ?? null;
                                            $childTitle = json_decode($childData[0]->title ?? '{}', true)[@$_COOKIE['k_language']] ?? 'Tanpa Judul';
                                            $childIcon = $childData[0]->icon ?? 'mdi mdi-circle';
                                        @endphp

                                        <li class="nav-item">
                                            @switch($childType)
                                                @case(2)
                                                @case(4)
                                                @case(7)
                                                @case(9)
                                                    <a class="nav-link {{ request()->get('id_menu') == $child->id ? 'active' : '' }}"
                                                       href="{{ route('cms::builder.posting.index', ['id_menu' => $child->id]) }}">
                                                        <i class="{{ $childIcon }} me-2"></i> {{ $childTitle }}
                                                    </a>
                                                    @break

                                                @case(3)
                                                    <a class="nav-link {{ request()->get('cat_id') == $child->id ? 'active' : '' }}"
                                                       href="{{ route('cms::builder.category.index', ['cat_id' => $child->id]) }}">
                                                        <i class="{{ $childIcon }} me-2"></i> {{ $childTitle }}
                                                    </a>
                                                    @break

                                                @case(5)
                                                    <a class="nav-link" href="{{ url(get_menu_id($child->id)->custom_links) }}">
                                                        <i class="{{ $childIcon }} me-2"></i> {{ $childTitle }}
                                                    </a>
                                                    @break

                                                @default
                                                    <a class="nav-link" href="#">
                                                        <i class="{{ $childIcon }} me-2"></i> {{ $childTitle }}
                                                    </a>
                                            @endswitch
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @else
                            <li class="nav-item pt-3">
                                @php
                                    $type = $needed[0]->type ?? null;
                                    $link = $type == 1
                                        ? route('cms::builder.posting.index', ['id_menu' => $menu->id])
                                        : url(get_menu_id($menu->id)->custom_links);
                                @endphp

                                <a class="nav-link" href="{{ $link }}">
                                    <i class="{{ $menuIcon }} me-2"></i> {{ $menuTitle }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                @endif

                <li class="nav-item pt-3">
                    <a class="nav-link" href="#">
                        <i class="mdi mdi-newspaper-variant me-2"></i> Newsletter & Subscription
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="mdi mdi-web me-2"></i> Site Configuration
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="sidebar-footer">
        <div class="rounded-3 d-flex align-items-center flex-row p-3" style="background: rgba(200, 200, 200, .1);">
            <div class="rounded-circle me-3"
                 style="width: 48px; height: 48px; background: url('{{ Auth::user()->profile_avatar_path }}') center center no-repeat; background-size: cover;">
            </div>
            <div class="flex-grow-1">
                <div class="fw-bold mb-0">{{ Str::limit(Auth::user()->name, 15) }}</div>
                <div class="small" style="color: rgba(150, 150, 150, .9)">
                    {{ Str::limit(Auth::user()->email_address, 20) }}
                </div>
            </div>
        </div>
    </div>
</div>

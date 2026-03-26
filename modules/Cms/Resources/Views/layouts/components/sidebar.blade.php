@php
    $lang = $_COOKIE['k_language'] ?? 'id';
    $kount_menu = json_decode(get_menu_order(), false);
@endphp

<ul class="metismenu list-unstyled" id="side-menu">
    {{-- Utama Section --}}
    <li class="nav-main-item">
        <a class="nav-main-link {{ request()->routeIs('cms::dashboard') ? 'active' : '' }}" href="{{ route('cms::dashboard') }}">
            <i class="nav-main-link-icon bx bxs-dashboard"></i>
            <span class="nav-main-link-name">Dashboard</span>
        </a>
    </li>

    <li class="menu-title" key="t-menu">Menu Dinamis</li>

    @if(!empty($kount_menu))
        @foreach ($kount_menu as $val)
            @php $menuData = get_needed($val->id)[0] ?? null; @endphp

            @if ($menuData && isset($val->children))
                {{-- Level 1 dengan Submenu --}}
                <li class="nav-main-item">
                    <a class="has-arrow waves-effect nav-main-link" href="javascript:void(0)">
                        <i class="nav-main-link-icon {{ $menuData->icon ?: 'bx bx-box' }}"></i>
                        <span class="nav-main-link-name">{{ json_decode($menuData->title, true)[$lang] ?? '' }}</span>
                    </a>
                    <ul class="sub-menu mm-collapse">
                        @foreach ($val->children as $valuet)
                            @php $childData = get_needed($valuet->id)[0] ?? null; @endphp

                            @if ($childData && isset($valuet->children))
                                {{-- Level 2 dengan Submenu --}}
                                <li class="nav-main-item">
                                    <a class="has-arrow nav-main-link" href="javascript:void(0)">
                                        <span class="nav-main-link-name">{{ json_decode($childData->title, true)[$lang] ?? '' }}</span>
                                    </a>
                                    <ul class="sub-menu mm-collapse">
                                        @foreach ($valuet->children as $vl)
                                            @php
                                                $item = get_needed($vl->id)[0];
                                                $itemTitle = json_decode($item->title, true)[$lang] ?? '';
                                            @endphp
                                            <li>
                                                @php
                                                    $href = '#';
                                                    if(in_array($item->type, ['2', '4', '7', '9'])) $href = route('cms::builder.posting.index', ['id_menu' => $vl->id]);
                                                    elseif($item->type == '3') $href = route('cms::builder.category.index', ['cat_id' => $vl->id]);
                                                    elseif($item->type == '5') $href = url(get_menu_id($vl->id)->custom_links);
                                                @endphp
                                                <a href="{{ $href }}" class="nav-main-link">
                                                    <span class="nav-main-link-name">{{ $itemTitle }}</span>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            @else
                                {{-- Level 2 Tanpa Submenu --}}
                                <li>
                                    @php
                                        $title = json_decode($childData->title, true)[$lang] ?? '';
                                        $href = '#';
                                        if(in_array($childData->type, ['2', '4', '7', '8', '9'])) $href = route('cms::builder.posting.index', ['id_menu' => $valuet->id]);
                                        elseif($childData->type == '3') $href = route('cms::builder.category.index', ['cat_id' => $valuet->id]);
                                        elseif($childData->type == '5') $href = url(get_menu_id($valuet->id)->custom_links);
                                    @endphp
                                    <a href="{{ $href }}" class="nav-main-link">
                                        <span class="nav-main-link-name">{{ $title }}</span>
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </li>
            @else
                {{-- Level 1 Tanpa Submenu --}}
                @if ($menuData)
                    <li class="nav-main-item">
                        @php
                            $title = json_decode($menuData->title, true)[$lang] ?? '';
                            $href = ($menuData->type != '1') ? url(get_menu_id($val->id)->custom_links) : route('cms::builder.posting.index', ['id_menu' => $val->id]);
                        @endphp
                        <a class="nav-main-link" href="{{ $href }}">
                            <i class="nav-main-link-icon {{ $menuData->icon ?: 'bx bx-file' }}"></i>
                            <span class="nav-main-link-name">{{ $title }}</span>
                        </a>
                    </li>
                @endif
            @endif
        @endforeach
    @endif

    <li class="menu-title" key="t-menu">Live Editor</li>
    <li class="nav-main-item">
        <a class="nav-main-link" href="{{ route('cms::live-editor-access') }}">
            <i class="nav-main-link-icon bx bx-user-circle"></i>
            <span class="nav-main-link-name">Access User</span>
        </a>
    </li>
</ul>

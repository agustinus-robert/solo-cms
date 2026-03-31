<nav class="navbar navbar-expand navbar-light bg-light border-bottom mb-2 py-2" style="height: 80px;">
    <div class="container-fluid px-3">
        <div class="navbar-nav align-items-center me-3">
            <button id="toggle-sidebar" class="btn btn-secondary bg-gray-200 border-0 p-2 rounded-3" style="width: 42px; height: 42px;"><i class="mdi mdi-menu h5 mb-0"></i></button>
            <span class="ms-3 fw-bold mb-0">@yield('navtitle', Str::limit(config('modules.core.name'), 12))</span>
        </div>
        <ul class="navbar-nav ms-auto d-flex align-items-center">
            <form class="d-none d-lg-block me-xl-5" action="" method="get">
                <div class="input-group">
                    <input name="query" type="search" class="form-control" placeholder="Find something here .." value="{{ request('query') }}">
                    <button class="btn btn-dark"><i class="mdi mdi-magnify"></i></button>
                </div>
            </form>
			<li class="nav-item ms-3 dropdown">
				<a class="nav-link" href="javascript:;" role="button" data-bs-toggle="dropdown">
					<i class="mdi mdi-headphones" style="font-size: 20px;"></i>
				</a>
				@include('components.support')
			</li>
            <li class="nav-item dropdown">
                <a class="nav-link" href="javascript:;" role="button" data-bs-toggle="dropdown">
                    <i class="mdi mdi-bell-outline h5 mb-0 p-2"></i>
                    @if(Auth::user()->notifications->whereNull('read_at')->count())
                        <span class="bg-danger text-white float-end rounded-circle pulse-danger" style="height: 8px; width: 8px;"></span>
                    @endif
                </a>
                @include('x-account::User.navbar-notifications-dropdown', ['user' => Auth::user()])
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link d-flex align-items-center" href="javascript:;" role="button" data-bs-toggle="dropdown">
                    <div class="rounded-circle me-sm-1" style="background: url('{{ Auth::user()->profile_avatar_path }}') center center no-repeat; background-size: cover; width: 42px; height: 42px;"></div>
                    <div class="d-none d-sm-flex flex-column ps-sm-2">
                        <strong class="mb-n1 text-nowrap">{{ Str::limit(Auth::user()->name, 20) }}</strong>
                        <small class="text-muted">{{ Auth::user()->email_address }}</small>
                    </div>
                </a>
				@include('x-account::User.navbar-accounts-dropdown')
            </li>
            <div class="form-check form-switch ms-3 d-none">
                <input class="form-check-input" type="checkbox" id="toggle-theme" value="1" checked>
                <label class="form-check-label" for="theme-toggle"></label>
            </div>
        </ul>
    </div>
</nav>
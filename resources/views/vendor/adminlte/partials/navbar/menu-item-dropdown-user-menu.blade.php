@php( $logout_url = View::getSection('logout_url') ?? config('adminlte.logout_url', 'logout') )
@php( $profile_url = View::getSection('profile_url') ?? config('adminlte.profile_url', 'logout') )

@if (config('adminlte.usermenu_profile_url', false))
    @php( $profile_url = Auth::user()->adminlte_profile_url() )
@endif

@if (config('adminlte.use_route_url', false))
    @php( $profile_url = $profile_url ? route($profile_url) : '' )
    @php( $logout_url = $logout_url ? route($logout_url) : '' )
@else
    @php( $profile_url = $profile_url ? url($profile_url) : '' )
    @php( $logout_url = $logout_url ? url($logout_url) : '' )
@endif

<li class="nav-item dropdown user-menu">

    {{-- User menu toggler --}}
    <a href="#" class="nav-link dropdown-toggle d-flex align-items-center px-3" data-toggle="dropdown">
        <div class="user-avatar-container me-2">
            @if(config('adminlte.usermenu_image'))
                <img src="{{ Auth::user()->adminlte_image() }}"
                     class="user-image img-circle border border-light shadow-xs"
                     alt="{{ Auth::user()->name }}"
                     style="width: 32px; height: 32px; object-fit: cover;">
            @else
                <div class="user-initial-avatar rounded-circle d-flex align-items-center justify-content-center bg-soft-blue text-primary fw-bold"
                     style="width: 32px; height: 32px; font-size: 0.8rem;">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
            @endif
        </div>
        <span class="d-none d-md-inline fw-600 text-dark small">
            {{ Auth::user()->name }}
        </span>
        <i class="fas fa-angle-down ms-2 text-muted small"></i>
    </a>

    {{-- User menu dropdown --}}
    <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">

        {{-- User menu header --}}
        @if(!View::hasSection('usermenu_header') && config('adminlte.usermenu_header'))
            <li class="user-header bg-white border-bottom d-flex flex-column align-items-center justify-content-center" style="height: auto; padding: 2rem 1rem;">
                @if(config('adminlte.usermenu_image'))
                    <img src="{{ Auth::user()->adminlte_image() }}"
                         class="img-circle border border-light mb-3"
                         alt="{{ Auth::user()->name }}"
                         style="width: 80px; height: 80px; object-fit: cover; box-shadow: var(--shadow-sm);">
                @else
                    <div class="user-initial-avatar rounded-circle d-flex align-items-center justify-content-center bg-soft-blue text-primary fw-bold mb-3"
                         style="width: 80px; height: 80px; font-size: 2rem;">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                @endif
                <h5 class="fw-bold text-dark mb-1">{{ Auth::user()->name }}</h5>
                @if(Auth::user()->
                
                roles->first())
                    <span class="badge bg-soft-blue text-primary border-0 px-3 py-2 mb-2" style="font-size: 0.8rem; font-weight: 600;">
                        <i class="fas fa-shield-alt me-1"></i> {{ Auth::user()->roles->first()->name }}
                    </span>
                @endif
                <p class="text-muted small mb-0">{{ Auth::user()->email }}</p>
            </li>
        @else
            @yield('usermenu_header')
        @endif

        {{-- Configured user menu links --}}
        @each('adminlte::partials.navbar.dropdown-item', $adminlte->menu("navbar-user"), 'item')

        {{-- User menu body --}}
        @hasSection('usermenu_body')
            <li class="user-body">
                @yield('usermenu_body')
            </li>
        @endif

        {{-- User menu footer --}}
        <li class="user-footer">
            @if($profile_url)
                <a href="{{ $profile_url }}" class="nav-link btn btn-default btn-flat d-inline-block">
                    <i class="fa fa-fw fa-user text-lightblue"></i>
                    {{ __('adminlte::menu.profile') }}
                </a>
            @endif
            <a class="btn btn-default btn-flat float-right @if(!$profile_url) btn-block @endif"
               href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fa fa-fw fa-power-off text-red"></i>
                {{ __('adminlte::adminlte.log_out') }}
            </a>
            <form id="logout-form" action="{{ $logout_url }}" method="POST" style="display: none;">
                @if(config('adminlte.logout_method'))
                    {{ method_field(config('adminlte.logout_method')) }}
                @endif
                {{ csrf_field() }}
            </form>
        </li>

    </ul>

</li>

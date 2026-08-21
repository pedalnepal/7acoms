@php
    $user = $user ?? auth('customer')->user();
    $initials = collect(explode(' ', trim($user->name ?? 'U')))
        ->filter()
        ->take(2)
        ->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))
        ->implode('');
@endphp

<aside class="user-sidebar">
    <div class="user-sidebar__profile">
        <div class="user-avatar" aria-hidden="true">
            <span>{{ $initials ?: 'U' }}</span>
        </div>
        <div class="user-sidebar__meta">
            <h6 class="mb-0 fw-bold text-truncate">{{ $user->name }}</h6>
            <small class="text-muted text-truncate d-block">{{ $user->email }}</small>
            @if($user->email_verified_at)
                <span class="user-badge user-badge--verified mt-2">
                    <i class="fa-solid fa-circle-check"></i> Verified
                </span>
            @endif
        </div>
    </div>

    <nav class="user-sidebar__nav" aria-label="Account navigation">
        <a href="{{ route('user.dashboard') }}"
           class="user-nav-link {{ request()->routeIs('user.dashboard') ? 'is-active' : '' }}">
            <span class="user-nav-link__icon"><i class="fa-solid fa-gauge-high"></i></span>
            <span class="user-nav-link__label">Dashboard</span>
            <i class="fa-solid fa-chevron-right user-nav-link__chevron"></i>
        </a>

        <a href="{{ route('user.orders') }}"
           class="user-nav-link {{ request()->routeIs('user.orders*') ? 'is-active' : '' }}">
            <span class="user-nav-link__icon"><i class="fa-solid fa-bag-shopping"></i></span>
            <span class="user-nav-link__label">My Orders</span>
            <i class="fa-solid fa-chevron-right user-nav-link__chevron"></i>
        </a>

        <a href="{{ route('user.cart.index') }}"
           class="user-nav-link {{ request()->routeIs('user.cart.*') || request()->routeIs('user.checkout.*') ? 'is-active' : '' }}">
            <span class="user-nav-link__icon"><i class="fa-solid fa-cart-shopping"></i></span>
            <span class="user-nav-link__label">My Cart</span>
            @php($cartCount = \App\Services\Cart::count())
            @if($cartCount > 0)
                <span class="badge bg-danger ms-auto">{{ $cartCount }}</span>
            @else
                <i class="fa-solid fa-chevron-right user-nav-link__chevron"></i>
            @endif
        </a>

        <a href="{{ route('user.profile') }}"
           class="user-nav-link {{ request()->routeIs('user.profile') ? 'is-active' : '' }}">
            <span class="user-nav-link__icon"><i class="fa-solid fa-user-gear"></i></span>
            <span class="user-nav-link__label">Profile Settings</span>
            <i class="fa-solid fa-chevron-right user-nav-link__chevron"></i>
        </a>

        <hr class="user-sidebar__divider">

        <a href="{{ route('user.logout') }}"
           class="user-nav-link user-nav-link--danger"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <span class="user-nav-link__icon"><i class="fa-solid fa-right-from-bracket"></i></span>
            <span class="user-nav-link__label">Logout</span>
        </a>
    </nav>
</aside>

<form id="logout-form" action="{{ route('user.logout') }}" method="POST" class="d-none">
    @csrf
</form>

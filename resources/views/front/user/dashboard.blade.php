@extends('front.common.layout')

@section('content')
@php
    $firstName = trim(explode(' ', (string) $user->name)[0] ?? $user->name);
    $hour      = (int) now()->format('H');
    $greeting  = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');

    $ordersCount    = isset($ordersCount)    ? $ordersCount    : (method_exists($user, 'orders') ? $user->orders()->count() : 0);
    $pendingCount   = isset($pendingCount)   ? $pendingCount   : (method_exists($user, 'orders') ? $user->orders()->where('status', 'pending')->count() : 0);
    $deliveredCount = isset($deliveredCount) ? $deliveredCount : (method_exists($user, 'orders') ? $user->orders()->where('status', 'delivered')->count() : 0);
    $totalSpent     = isset($totalSpent)     ? $totalSpent     : (method_exists($user, 'orders') ? (float) $user->orders()->sum('total_amount') : 0);
    $recentOrders   = isset($recentOrders)   ? $recentOrders   : (method_exists($user, 'orders') ? $user->orders()->latest()->take(5)->get() : collect());
@endphp

<section class="user-dashboard-area">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3">
                @include('front.user.sidebar')
            </div>

            <div class="col-lg-9">
                {{-- Welcome banner --}}
                <div class="user-hero">
                    <div class="user-hero__content">
                        <span class="user-hero__kicker">
                            <i class="fa-solid fa-seedling"></i> Member Dashboard
                        </span>
                        <h1 class="user-hero__title">
                            {{ $greeting }}, <span>{{ $firstName }}</span>!
                        </h1>
                        <p class="user-hero__subtitle">
                            Here's a snapshot of your account activity. Manage your orders,
                            update your profile and track everything in one place.
                        </p>
                        <div class="user-hero__actions">
                            <a href="{{ route('user.orders') }}" class="btn btn-light btn-pill fw-semibold">
                                <i class="fa-solid fa-bag-shopping me-2"></i> View Orders
                            </a>
                            <a href="{{ route('user.profile') }}" class="btn btn-outline-light btn-pill fw-semibold">
                                <i class="fa-solid fa-user-pen me-2"></i> Edit Profile
                            </a>
                        </div>
                    </div>
                    <div class="user-hero__decor" aria-hidden="true">
                        <i class="fa-solid fa-leaf"></i>
                    </div>
                </div>

                {{-- Stats cards --}}
                <div class="row g-3 g-md-4 mt-1">
                    <div class="col-sm-6 col-xl-3">
                        <div class="stat-card stat-card--primary">
                            <div class="stat-card__icon"><i class="fa-solid fa-bag-shopping"></i></div>
                            <div class="stat-card__body">
                                <span class="stat-card__label">Total Orders</span>
                                <span class="stat-card__value">{{ $ordersCount }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="stat-card stat-card--warning">
                            <div class="stat-card__icon"><i class="fa-solid fa-clock"></i></div>
                            <div class="stat-card__body">
                                <span class="stat-card__label">Pending</span>
                                <span class="stat-card__value">{{ $pendingCount }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="stat-card stat-card--success">
                            <div class="stat-card__icon"><i class="fa-solid fa-circle-check"></i></div>
                            <div class="stat-card__body">
                                <span class="stat-card__label">Delivered</span>
                                <span class="stat-card__value">{{ $deliveredCount }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="stat-card stat-card--info">
                            <div class="stat-card__icon"><i class="fa-solid fa-wallet"></i></div>
                            <div class="stat-card__body">
                                <span class="stat-card__label">Total Spent</span>
                                <span class="stat-card__value">Rs. {{ number_format((float) $totalSpent, 0) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Quick actions + Account info --}}
                <div class="row g-4 mt-1">
                    <div class="col-lg-7">
                        <div class="user-card h-100">
                            <div class="user-card__header">
                                <h5 class="mb-0">Quick Actions</h5>
                                <span class="text-muted small">Common shortcuts</span>
                            </div>
                            <div class="user-card__body">
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <a href="{{ route('home') }}" class="quick-action">
                                            <span class="quick-action__icon" style="background:rgba(43,140,0,.12); color:var(--primary);">
                                                <i class="fa-solid fa-store"></i>
                                            </span>
                                            <span>
                                                <strong class="d-block">Browse Shop</strong>
                                                <small class="text-muted">Discover new seeds</small>
                                            </span>
                                        </a>
                                    </div>
                                    <div class="col-sm-6">
                                        <a href="{{ route('user.orders') }}" class="quick-action">
                                            <span class="quick-action__icon" style="background:rgba(255,193,7,.18); color:#b88500;">
                                                <i class="fa-solid fa-truck-fast"></i>
                                            </span>
                                            <span>
                                                <strong class="d-block">Track Orders</strong>
                                                <small class="text-muted">View delivery status</small>
                                            </span>
                                        </a>
                                    </div>
                                    <div class="col-sm-6">
                                        <a href="{{ route('user.profile') }}" class="quick-action">
                                            <span class="quick-action__icon" style="background:rgba(13,110,253,.12); color:#0d6efd;">
                                                <i class="fa-solid fa-id-card"></i>
                                            </span>
                                            <span>
                                                <strong class="d-block">Profile</strong>
                                                <small class="text-muted">Update your details</small>
                                            </span>
                                        </a>
                                    </div>
                                    <div class="col-sm-6">
                                        <a href="{{ route('user.profile') }}" class="quick-action">
                                            <span class="quick-action__icon" style="background:rgba(220,53,69,.12); color:#dc3545;">
                                                <i class="fa-solid fa-shield-halved"></i>
                                            </span>
                                            <span>
                                                <strong class="d-block">Security</strong>
                                                <small class="text-muted">Change password</small>
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="user-card h-100">
                            <div class="user-card__header">
                                <h5 class="mb-0">Account Information</h5>
                                <span class="user-badge user-badge--verified">
                                    <i class="fa-solid fa-circle-check"></i> Active
                                </span>
                            </div>
                            <div class="user-card__body">
                                <ul class="info-list">
                                    <li>
                                        <span class="info-list__label"><i class="fa-solid fa-user"></i> Full Name</span>
                                        <span class="info-list__value">{{ $user->name }}</span>
                                    </li>
                                    <li>
                                        <span class="info-list__label"><i class="fa-solid fa-envelope"></i> Email</span>
                                        <span class="info-list__value text-truncate">{{ $user->email }}</span>
                                    </li>
                                    <li>
                                        <span class="info-list__label"><i class="fa-solid fa-calendar"></i> Member Since</span>
                                        <span class="info-list__value">
                                            {{ optional($user->created_at)->format('M d, Y') ?? '—' }}
                                        </span>
                                    </li>
                                    <li>
                                        <span class="info-list__label"><i class="fa-solid fa-shield-halved"></i> Email Verified</span>
                                        <span class="info-list__value">
                                            @if($user->email_verified_at)
                                                <span class="text-success fw-semibold">
                                                    <i class="fa-solid fa-check"></i> Yes
                                                </span>
                                            @else
                                                <span class="text-warning fw-semibold">
                                                    <i class="fa-solid fa-triangle-exclamation"></i> Pending
                                                </span>
                                            @endif
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Recent orders --}}
                <div class="user-card mt-4">
                    <div class="user-card__header">
                        <h5 class="mb-0">Recent Orders</h5>
                        <a href="{{ route('user.orders') }}" class="text-decoration-none small fw-semibold" style="color:var(--primary);">
                            View all <i class="fa-solid fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="user-card__body p-0">
                        @if($recentOrders->isEmpty())
                            <div class="user-empty">
                                <div class="user-empty__icon">
                                    <i class="fa-solid fa-box-open"></i>
                                </div>
                                <h6 class="mb-1">No orders yet</h6>
                                <p class="text-muted mb-3">Start exploring our products and place your first order.</p>
                                <a href="{{ route('home') }}" class="btn btn-primary-custom">
                                    <i class="fa-solid fa-cart-plus me-2"></i> Start Shopping
                                </a>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table user-table mb-0 align-middle">
                                    <thead>
                                        <tr>
                                            <th>Order</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentOrders as $order)
                                            <tr>
                                                <td>
                                                    <strong>#{{ $order->order_number ?? $order->id }}</strong>
                                                </td>
                                                <td>{{ optional($order->created_at)->format('M d, Y') }}</td>
                                                <td>
                                                    @php
                                                        $statusMap = [
                                                            'pending'    => 'warning',
                                                            'processing' => 'info',
                                                            'shipped'    => 'primary',
                                                            'delivered'  => 'success',
                                                            'cancelled'  => 'danger',
                                                        ];
                                                        $statusColor = $statusMap[strtolower($order->status ?? '')] ?? 'secondary';
                                                    @endphp
                                                    <span class="status-pill status-pill--{{ $statusColor }}">
                                                        {{ ucfirst($order->status ?? 'Unknown') }}
                                                    </span>
                                                </td>
                                                <td class="text-end fw-semibold">
                                                    Rs. {{ number_format((float) ($order->total_amount ?? 0), 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

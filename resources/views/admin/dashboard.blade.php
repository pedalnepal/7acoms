@extends('admin.common.layout')

@section('content')
@include('admin.common.flash')

{{-- Welcome --}}
<div class="card dash-card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <div class="admin-welcome-avatar">
                <span>{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
            </div>
            <div>
                <h1 class="admin-page-title mb-1">Welcome back, {{ auth()->user()->name }}</h1>
                <p class="text-muted mb-0">{{ now()->format('l, d F Y') }} &mdash; {{ config('app.name', '') }} Admin Panel</p>
            </div>
        </div>
    </div>
</div>

{{-- Overview --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-4">
        <a href="{{ route('registration.index') }}" class="dashboard-stat-link">
            <div class="card admin-panel-card dashboard-stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="dashboard-stat-icon"><i class="fa-solid fa-user-plus"></i></div>
                    <div>
                        <div class="dashboard-stat-value">{{ number_format($totalRegistrations) }}</div>
                        <div class="dashboard-stat-label">Total Registrations</div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-lg-4">
        <a href="{{ route('abstract.index') }}" class="dashboard-stat-link">
            <div class="card admin-panel-card dashboard-stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="dashboard-stat-icon"><i class="fa-solid fa-file-lines"></i></div>
                    <div>
                        <div class="dashboard-stat-value">{{ number_format($totalAbstracts) }}</div>
                        <div class="dashboard-stat-label">Total Abstract Submissions</div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

@endsection

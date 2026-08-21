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


@endsection

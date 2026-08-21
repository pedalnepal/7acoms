@extends('front.common.layout')

@section('content')
@php
    $initials = collect(explode(' ', trim($user->name ?? 'U')))
        ->filter()
        ->take(2)
        ->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))
        ->implode('');
@endphp

<section class="user-dashboard-area">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3">
                @include('front.user.sidebar')
            </div>

            <div class="col-lg-9">
                <div class="user-page-header">
                    <div>
                        <h2 class="user-page-header__title">Profile Settings</h2>
                        <p class="user-page-header__subtitle mb-0">
                            Manage your personal information and account security.
                        </p>
                    </div>
                </div>

                @if(session('status'))
                    <div class="alert alert-success rounded-3 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-circle-check"></i>
                        <div>{{ session('status') }}</div>
                    </div>
                @endif

                <div class="row g-4">
                    {{-- Profile summary card --}}
                    <div class="col-lg-4">
                        <div class="user-card user-profile-summary text-center h-100">
                            <div class="user-card__body">
                                <div class="user-avatar user-avatar--lg mx-auto mb-3">
                                    <span>{{ $initials ?: 'U' }}</span>
                                </div>
                                <h5 class="mb-1">{{ $user->name }}</h5>
                                <p class="text-muted small mb-3 text-truncate">{{ $user->email }}</p>

                                @if($user->email_verified_at)
                                    <span class="user-badge user-badge--verified">
                                        <i class="fa-solid fa-circle-check"></i> Verified Account
                                    </span>
                                @else
                                    <span class="user-badge user-badge--pending">
                                        <i class="fa-solid fa-triangle-exclamation"></i> Email Not Verified
                                    </span>
                                @endif

                                <hr class="my-4">

                                <ul class="info-list info-list--compact text-start">
                                    <li>
                                        <span class="info-list__label"><i class="fa-solid fa-calendar"></i> Joined</span>
                                        <span class="info-list__value">
                                            {{ optional($user->created_at)->format('M d, Y') ?? '—' }}
                                        </span>
                                    </li>
                                    <li>
                                        <span class="info-list__label"><i class="fa-solid fa-bag-shopping"></i> Orders</span>
                                        <span class="info-list__value">
                                            {{ method_exists($user, 'orders') ? $user->orders()->count() : 0 }}
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Profile + password forms --}}
                    <div class="col-lg-8">
                        <div class="user-card mb-4">
                            <div class="user-card__header">
                                <h5 class="mb-0">Personal Information</h5>
                                <small class="text-muted">Update your account's profile details</small>
                            </div>
                            <div class="user-card__body">
                                <form action="{{ route('user.profile.update') }}" method="POST" class="user-form">
                                    @csrf
                                    @method('PUT')
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Full Name</label>
                                            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                                   class="form-control" placeholder="Your full name">
                                            @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Email Address</label>
                                            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                                   class="form-control" placeholder="you@example.com">
                                            @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-12 d-flex justify-content-end gap-2 mt-2">
                                            <button type="reset" class="btn btn-outline-secondary btn-pill px-4">Cancel</button>
                                            <button type="submit" class="btn btn-primary-custom">
                                                <i class="fa-solid fa-floppy-disk me-2"></i> Save Changes
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="user-card">
                            <div class="user-card__header">
                                <h5 class="mb-0">Change Password</h5>
                                <small class="text-muted">Use a strong, unique password</small>
                            </div>
                            <div class="user-card__body">
                                <form action="{{ route('user.profile.password') }}" method="POST" class="user-form">
                                    @csrf
                                    @method('PUT')
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <label class="form-label">Current Password</label>
                                            <div class="password-field">
                                                <input type="password" name="current_password" class="form-control"
                                                       placeholder="Enter current password">
                                                <button type="button" class="password-toggle" aria-label="Show password">
                                                    <i class="fa-solid fa-eye"></i>
                                                </button>
                                            </div>
                                            @error('current_password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">New Password</label>
                                            <div class="password-field">
                                                <input type="password" name="password" class="form-control"
                                                       placeholder="New password">
                                                <button type="button" class="password-toggle" aria-label="Show password">
                                                    <i class="fa-solid fa-eye"></i>
                                                </button>
                                            </div>
                                            @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Confirm Password</label>
                                            <div class="password-field">
                                                <input type="password" name="password_confirmation" class="form-control"
                                                       placeholder="Confirm new password">
                                                <button type="button" class="password-toggle" aria-label="Show password">
                                                    <i class="fa-solid fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-12 d-flex justify-content-end gap-2 mt-2">
                                            <button type="submit" class="btn btn-primary-custom">
                                                <i class="fa-solid fa-shield-halved me-2"></i> Update Password
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.password-field {
    position: relative;
}

.password-field .form-control {
    padding-right: 45px;
}

.password-toggle {
    position: absolute;
    top: 50%;
    right: 12px;
    transform: translateY(-50%);
    background: none;
    border: none;
    padding: 0;
    color: #888;
    line-height: 1;
}

.password-toggle:hover {
    color: #333;
}
</style>

@push('scripts')
<script>
document.querySelectorAll('.password-toggle').forEach(function (toggle) {
    toggle.addEventListener('click', function () {
        var input = toggle.previousElementSibling;
        var icon = toggle.querySelector('i');
        var isHidden = input.type === 'password';

        input.type = isHidden ? 'text' : 'password';
        icon.classList.toggle('fa-eye', !isHidden);
        icon.classList.toggle('fa-eye-slash', isHidden);
        toggle.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
    });
});
</script>
@endpush
@endsection

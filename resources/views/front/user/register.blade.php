@extends('front.common.layout')

@section('content')

<section class="login-form-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 col-sm-12">

                <div class="login-form card p-4 p-md-5 shadow-sm">

                    <div class="mb-4">
                        <h2 class="login-form-title">Create Account</h2>

                        <p class="login-form-subtitle mb-0">
                            Create your account to continue.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('user.register.submit') }}">
                        @csrf

                        <div class="form-group">
                            <input
                                type="text"
                                name="name"
                                value="{{ old('name') }}"
                                class="form-control"
                                placeholder="Full Name"
                                required
                                autofocus
                            >

                            @error('name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <input
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                class="form-control"
                                placeholder="Email Address"
                                required
                            >

                            @error('email')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="password-field">
                                <input
                                    type="password"
                                    name="password"
                                    class="form-control"
                                    placeholder="Password"
                                    minlength="8"
                                    required
                                >
                                <button type="button" class="password-toggle" aria-label="Show password">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                            <small class="text-muted">Must be at least 8 characters.</small>

                            @error('password')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="password-field">
                                <input
                                    type="password"
                                    name="password_confirmation"
                                    class="form-control"
                                    placeholder="Confirm Password"
                                    required
                                >
                                <button type="button" class="password-toggle" aria-label="Show password">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Register
                        </button>

                        <div class="text-center mt-3">
                            Already have an account?
                            <a href="{{ route('user.login') }}">
                                Login
                            </a>
                        </div>

                    </form>

                </div>

            </div>
        </div>
    </div>
</section>

<style>

.login-form-section {
    padding: 80px 0;
}

.login-form {
    background: #fff;
    border-radius: 12px;
}

.login-form-title {
    font-size: 30px;
    font-weight: 700;
    color: #222;
}

.login-form-subtitle {
    color: #666;
    font-size: 15px;
}

.form-control {
    width: 100%;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 14px 15px;
    margin-bottom: 15px;
    transition: 0.3s;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 3px rgba(0,123,255,0.15);
}

.text-danger {
    font-size: 14px;
    margin-top: -10px;
    margin-bottom: 10px;
}

.btn-primary {
    border: none;
    padding: 14px;
    border-radius: 8px;
    font-weight: 600;
}

/* Password eye toggle */
.password-field {
    position: relative;
}

.password-field .form-control {
    padding-right: 45px;
}

.password-toggle {
    position: absolute;
    top: 20px;
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
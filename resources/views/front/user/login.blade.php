@extends('front.common.layout')

@section('content')
<section class="login-form-section">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-6 col-md-8 col-sm-12">
        <div class="login-form card p-4 p-md-5 shadow-sm">
          <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap">
            <div>
              <h2 class="login-form-title">User Login</h2>
              <p class="login-form-subtitle mb-0">
                Welcome back! Please enter your credentials to access your account.
              </p>
            </div>
          </div>

          @if (session('status'))
            <div class="alert alert-success">
              {{ session('status') }}
            </div>
          @endif

          <form method="POST" action="{{ route('user.login.submit') }}">
              @csrf

              <div class="form-group">
                <input type="email" name="email" value="{{ old('email') }}" placeholder="Email" class="form-control" required autofocus>
                @error('email') <div class="text-danger">{{ $message }}</div> @enderror
              </div>

              <div class="form-group">
                <div class="password-field">
                    <input type="password" name="password" placeholder="Password" class="form-control" required>
                    <button type="button" class="password-toggle" aria-label="Show password">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
                @error('password') <div class="text-danger">{{ $message }}</div> @enderror
              </div>

              <div class="form-group d-flex justify-content-between align-items-center">
                <label class="custom-checkbox">
                    <input type="checkbox" name="remember" value="1">
                    Remember me
                </label>
                <a href="{{ route('user.password.request') }}" class="forgot-password-link">Forgot password?</a>
              </div>

              <button type="submit" class="btn btn-primary w-100">Login</button>

              <div class="text-center mt-3">
                  Don't have an account?
                  <a href="{{ route('user.register') }}">
                      Register
                  </a>
              </div>

          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<style>


/* Login Form Section */
.login-form-section {
  padding: 80px 0;
}

.login-form {
  background-color: #fff;
  border-radius: 10px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.login-form-title {
  font-size: 30px;
  font-weight: bold;
  color: #333;
  margin-bottom: 10px;
}

.login-form-subtitle {
  font-size: 16px;
  color: #555;
}

.alert-success {
  background-color: #e6f7ee;
  color: #1e7e4d;
  border: 1px solid #c3ecd7;
  border-radius: 8px;
  padding: 12px 15px;
  font-size: 14px;
  margin: 15px 0;
}

/* Form Input Styles */
.form-control {
  border: 1px solid #ddd;
  padding: 15px;
  border-radius: 8px;
  width: 100%;
  margin-bottom: 15px;
  font-size: 16px;
  transition: border-color 0.3s ease;
}

.form-control:focus {
  border-color: #007bff;
  box-shadow: 0 0 10px rgba(0, 123, 255, 0.2);
}

.text-danger {
  color: #e74c3c;
  font-size: 14px;
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

/* Checkbox & Remember Me */
.custom-checkbox input {
  margin-right: 8px;
}

.forgot-password-link {
  font-size: 14px;
  color: #007bff;
  text-decoration: none;
}

.forgot-password-link:hover {
  text-decoration: underline;
}

/* Button */
.btn {
  padding: 12px;
  border-radius: 8px;
  font-size: 16px;
  font-weight: bold;
}

.btn-primary {
  background-color: #007bff;
  border: none;
  color: white;
}

.btn-primary:hover {
  background-color: #0056b3;
}

/* Responsive Design */
@media (max-width: 768px) {
  .login-form {
    padding: 20px;
  }

  .login-form-title {
    font-size: 26px;
  }
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

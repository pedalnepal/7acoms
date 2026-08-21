@extends('front.common.layout')

@section('content')
<section class="login-form-section">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-6 col-md-8 col-sm-12">
        <div class="login-form card p-4 p-md-5 shadow-sm text-center">

          <div class="verify-icon mx-auto mb-3">
            <i class="fa fa-envelope"></i>
          </div>

          <h2 class="login-form-title">Verify Your Email</h2>
          <p class="login-form-subtitle">
            We've sent a verification link to
            @if (auth('customer')->check())
              <strong>{{ auth('customer')->user()->email }}</strong>.
            @else
              your email address.
            @endif
            Click the link to activate your account.
          </p>

          @if (session('status') === 'verification-link-sent')
            <div class="alert alert-success">
              A new verification link has been sent to your email address.
            </div>
          @elseif (session('status'))
            <div class="alert alert-success">
              {{ session('status') }}
            </div>
          @endif

          <p class="text-muted small mb-4">
            Didn't receive the email? Check your spam folder, or click below to resend it.
          </p>

          <form method="POST" action="{{ route('user.verification.send') }}">
              @csrf
              <button type="submit" class="btn btn-primary w-100">Resend Verification Email</button>
          </form>

          <form method="POST" action="{{ route('user.logout') }}" class="mt-3">
              @csrf
              <button type="submit" class="btn btn-outline-secondary w-100">Logout</button>
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

.verify-icon {
  width: 70px;
  height: 70px;
  border-radius: 50%;
  background-color: rgba(0, 123, 255, 0.1);
  color: #007bff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 28px;
}

.alert-success {
  background-color: #e6f7ee;
  color: #1e7e4d;
  border: 1px solid #c3ecd7;
  border-radius: 8px;
  padding: 12px 15px;
  font-size: 14px;
  margin-bottom: 15px;
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

.btn-outline-secondary {
  background-color: transparent;
  border: 1px solid #ccc;
  color: #555;
}

.btn-outline-secondary:hover {
  background-color: #f5f5f5;
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
@endsection

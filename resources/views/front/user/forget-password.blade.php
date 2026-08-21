@extends('front.common.layout')

@section('content')
<section class="login-form-section">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-6 col-md-8 col-sm-12">
        <div class="login-form card p-4 p-md-5 shadow-sm text-center">

          <div class="verify-icon mx-auto mb-3">
            <i class="fa fa-key"></i>
          </div>

          <h2 class="login-form-title">Forgot Password?</h2>
          <p class="login-form-subtitle mb-4">
            No worries. Enter the email address linked to your account and
            we'll send you a link to reset your password.
          </p>

          @if (session('status'))
            <div class="alert alert-success">
              {{ session('status') }}
            </div>
          @endif

          <form method="POST" action="{{ route('user.password.email') }}" class="text-start">
              @csrf

              <div class="form-group">
                <input type="email" name="email" value="{{ old('email') }}" placeholder="Email" class="form-control" required autofocus>
                @error('email') <div class="text-danger">{{ $message }}</div> @enderror
              </div>

              <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>

              <div class="text-center mt-3">
                  Remembered your password?
                  <a href="{{ route('user.login') }}">Back to login</a>
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
@endsection

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Login</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  {{-- <link rel="stylesheet" href="{{ asset('/new_admin/css/all.min.css') }}"> --}}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
  integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
  crossorigin="anonymous" referrerpolicy="no-referrer" />
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('asset/new_admin/css/adminlte.min.css') }}">
  <link href="{{ asset('asset/new_admin/css/admin_main_style.css') }}" rel="stylesheet" />

</head>
<body class="hold-transition login-page">
  @push('style')
@endpush
  <style>

  </style>
<main class="login-main" style="display: grid; place-items: center; width: 100%; height: 100vh">
  <div class="row w-100 mx-0 auth-page">
    <div class="col-md-7 col-xl-5 mx-auto">
      <div class=" login-card">
        <div class="row">
          <div class=" col-md-4 p-0" style="overflow: hidden">
            <div class="login-card-left auth-side-wrapper">
  
            </div>
          </div>
          <div class="login-card-right col-md-8">
            <div class="auth-form-wrapper px-4 py-5">
              <h5 href="#" class="text-muted d-block mb-2">Admin</h5>
              <h5 class="fw-normal mb-3" style="color: #295881;">Welcome back! Log in to your account.</h5>
              <form class="forms-sample" action="{{ route('custom.login.submit') }}" method="POST">
               @csrf
                <div class="input-group email-group">
                  <span class="loginicon"><i class="fa-solid fa-envelope"></i></span>
                  <input class="logininput" type="email" name="email" placeholder="Email Address">
                  @error('email')
                  <div class="error-message">
                  <span><i class="fa-solid fa-circle-exclamation"></i></span>  {{ $message }}
                  </div>
                  @enderror
                </div>
                <div class="input-group password-group">
                  <span class="loginicon"><i class="fa-solid fa-lock"></i></span>
                  <input class="logininput" type="password" name="password" placeholder="Password">
                  @error('password')
                  <div class="error-message">
                    <span><i class="fa-solid fa-circle-exclamation"></i></span>  {{ $message }}
                  </div>
                  @enderror
                </div>
                <div style="margin-left: 10px">
                <div class="checkbox-wrapper-46 mb-1 ms-1 w-100">
                 <input type="checkbox" id="cbx-46" name="remember" value="1" class="inp-cbx" 
    {{ old('remember') || $remember ? 'checked' : '' }}>
                  
                  <label for="cbx-46" class="cbx">
                    <span>
                      <svg viewBox="0 0 12 10" height="10px" width="12px">
                        <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                      </svg>
                    </span>
                    <span style="letter-spacing: 0; font-weight: 400; color: #000000;">Remember me</span>
                  </label>
                </div>
         
                  <button class="loginbtn">
                    <span>Login</span>
                    <svg id="arrow-horizontal" xmlns="http://www.w3.org/2000/svg" width="30" height="10" viewBox="0 0 46 16">
                      <path id="Path_10" data-name="Path 10" d="M8,0,6.545,1.455l5.506,5.506H-30V9.039H12.052L6.545,14.545,8,16l8-8Z" transform="translate(30)"></path>
                    </svg>
                  </button>
                </div>
              </form>
            </div>
          </div>
          
        </div>
      </div>
    </div>
  </div>
</main>
<!-- /.login-box -->

<!-- Bootstrap 4 -->
<script src="{{ asset('asset/new_admin/assets/plugins/bootstrap/bootstrap.bundle.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('asset/new_admin/js/adminlte.min.js') }}"></script>
</body>
</html>

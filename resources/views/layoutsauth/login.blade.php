<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>InternSight | Login</title>

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:300,400,600,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="/AdminLTE/plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="/AdminLTE/dist/css/adminlte.min.css?v=3.2.0">
  <style>
    /* Previous styles remain the same until .form-control */
    body {
      background-image: url("../assets/backgroundlogin.png");
      background-size: cover;
      color: #fff;
      font-family: 'Nunito', sans-serif;
    }

    .register-box {
      width: 400px;
      margin: 50px auto;
    }

    .register-logo a {
      font-size: 2rem;
      font-weight: bold;
      color: #fff;
      text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.2);
    }

    .btn-black {
      background-color: black;
      color: white;
    }

    .btn-black:hover {
      color: white;
    }

    .btn-white {
      background-color: white;
      color: black;
    }

    .btn-white:hover {
      color: black;
    }

    .card {
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
      background-color: rgba(255, 255, 255, 0.1) !important;
      backdrop-filter: blur(10px);
    }

    .card-body {
      padding: 30px;
    }

    .register-card-body {
      background-color: transparent !important;
    }

    .btn-primary {
      background: #2575fc;
      border: none;
      font-weight: bold;
    }

    .btn-primary:hover {
      background: #1a5bb8;
    }

    /* Updated input and icon styles */
    .form-control {
      border-radius: 0.25rem !important;
      background-color: rgba(255, 255, 255, 0.2);
      border: none;
      color: white;
      padding-right: 40px;
    }

    .form-control::placeholder {
      color: rgba(255, 255, 255, 0.7);
    }

    .form-control:focus {
      background-color: rgba(255, 255, 255, 0.3);
      color: white;
      box-shadow: none;
    }

    .input-group {
      position: relative;
    }

    .input-group-append {
      position: absolute;
      right: 0;
      top: 0;
      bottom: 0;
      z-index: 10;
    }

    .input-group-text {
      background-color: rgba(255, 255, 255, 0.2);
      border: none;
      color: white !important;
      padding: 0 15px;
      height: 100%;
      display: flex;
      align-items: center;
      cursor: pointer;
      border-radius: 0 20px 20px 0 !important;
    }

    .input-group-text:hover {
      background-color: rgba(255, 255, 255, 0.3);
    }

    .fas {
      color: white;
    }

    .alert {
      border-radius: 10px;
      text-align: center;
      border: none;
    }

    .login-box-msg {
      color: white;
    }

    .btn-secondary {
      background-color: #FF6B6B;
      border: none;
    }

    .btn-secondary:hover {
      background-color: #FF5252;
    }

    /* Ensure text color remains visible on auto-fill */
    input:-webkit-autofill,
    input:-webkit-autofill:hover,
    input:-webkit-autofill:focus,
    input:-webkit-autofill:active {
      -webkit-box-shadow: 0 0 0 30px rgba(255, 255, 255, 0.2) inset !important;
      -webkit-text-fill-color: white !important;
      transition: background-color 5000s ease-in-out 0s;
      /* Prevent background color change */
    }

    input:-webkit-autofill::first-line {
      color: white !important;
    }
  </style>
</head>

<body class="hold-transition register-page">
  <div class="register-box">
    <div class="register-logo">
      <a href="#" style="pointer-events:none; text-shadow:3px 3px 5px black" class="">InternSight</a>
    </div>

    <div class="card">
      <div class="card-body register-card-body">
        <p class="login-box-msg">Login Terlebih Dahulu</p>

        @if (session('success'))
        <div class="alert alert-success">
          {{ session('success') }}
        </div>
        @endif
        @if (session('error'))
        <div class="alert alert-danger">          
          <p class=" m-0">{{ session('error') }}</p>
        </div>
        @endif

        <form action="{{ route('dologin') }}" method="post">
          @csrf
          <div class="input-group mb-3">
            <input type="email" class="form-control" name="email" placeholder="Email" value="{{old('email')}}" required>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-envelope"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
            <div class="input-group-append">
              <div class="input-group-text" onclick="togglePassword()">
                <span class="fas fa-eye" id="toggleIcon"></span>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <button type="submit" class="btn btn-black btn-block">Login</button>
            </div>
          </div>
          <div class="row mt-3">
            <div class="col-12">
              <a href="{{ route('welcome') }}" class="btn btn-white btn-block">Back</a>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="/AdminLTE/plugins/jquery/jquery.min.js"></script>
  <script src="/AdminLTE/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="/AdminLTE/dist/js/adminlte.min.js"></script>
  <script>
    function togglePassword() {
      const passwordInput = document.getElementById('password');
      const toggleIcon = document.getElementById('toggleIcon');

      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
      } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
      }
    }
  </script>
</body>

</html>
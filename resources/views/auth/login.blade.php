<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Log In | Silva - Responsive Admin Dashboard Template</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}" sizes="16x16" />

    <!-- remix icon font css -->
    <link rel="stylesheet" href="{{ asset('assets/css/remixicon.css') }}" />
    <!-- BootStrap css -->
    <link rel="stylesheet" href="{{ asset('assets/css/lib/bootstrap.min.css') }}" />
    <!-- Apex Chart css -->
    <link rel="stylesheet" href="{{ asset('assets/css/lib/apexcharts.css') }}" />
    <!-- Data Table css -->
    <link rel="stylesheet" href="{{ asset('assets/css/lib/dataTables.min.css') }}" />
    <!-- Text Editor css -->
    <link rel="stylesheet" href="{{ asset('assets/css/lib/editor-katex.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/lib/editor.atom-one-dark.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/lib/editor.quill.snow.css') }}" />
    <!-- Date picker css -->
    <link rel="stylesheet" href="{{ asset('assets/css/lib/flatpickr.min.css') }}" />
    <!-- Calendar css -->
    <link rel="stylesheet" href="{{ asset('assets/css/lib/full-calendar.css') }}" />
    <!-- Vector Map css -->
    <link rel="stylesheet" href="{{ asset('assets/css/lib/jquery-jvectormap-2.0.5.css') }}" />
    <!-- Popup css -->
    <link rel="stylesheet" href="{{ asset('assets/css/lib/magnific-popup.css') }}" />
    <!-- Slick Slider css -->
    <link rel="stylesheet" href="{{ asset('assets/css/lib/slick.css') }}" />
    <!-- prism css -->
    <link rel="stylesheet" href="{{ asset('assets/css/lib/prism.css') }}" />
    <!-- file upload css -->
    <link rel="stylesheet" href="{{ asset('assets/css/lib/file-upload.css') }}" />
    <!-- audioplayer css -->
    <link rel="stylesheet" href="{{ asset('assets/css/lib/audioplayer.css') }}" />
    <!-- main css -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" />
</head>
<body>
<section class="auth bg-base d-flex flex-wrap">
    <div class="auth-left d-lg-block d-none">
        <div class="d-flex align-items-center flex-column h-100 justify-content-center">
            <img src="{{ asset('assets/images/auth/auth-img.png') }}" alt="">
        </div>
    </div>

    <div class="auth-right py-32 px-24 d-flex flex-column justify-content-center">
        <div class="max-w-464-px mx-auto w-100">
            <div class="text-center mb-40">
                <a href="{{ url('/') }}" class="max-w-290-px d-inline-block">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="logo">
                </a>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <h4 class="mb-12">Welcome back</h4>
            <p class="mb-32 text-secondary-light text-lg">Sign in to continue to Silva.</p>

            <form id="loginForm" action="{{ route('login') }}" method="POST">
                @csrf

                <div class="row mb-32">
                    <div class="col-6">
                        <a href="{{ route('google.login') }}" class="btn text-primary-600 border d-flex align-items-center justify-content-center w-100 gap-2">
                            <iconify-icon icon="logos:google-icon" class="text-xl"></iconify-icon>
                            Google
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('facebook.login') }}" class="btn text-primary-600 border d-flex align-items-center justify-content-center w-100 gap-2">
                            <iconify-icon icon="ic:baseline-facebook" class="text-xl"></iconify-icon>
                            Facebook
                        </a>
                    </div>
                </div>

                <div class="center-border-horizontal text-center mb-32">
                    <span class="bg-base z-1 px-4">or continue with email</span>
                </div>

                <div class="icon-field mb-16">
                    <span class="icon top-50 translate-middle-y">
                        <iconify-icon icon="mage:email"></iconify-icon>
                    </span>
                    <input type="email"
                           name="email"
                           id="emailaddress"
                           class="form-control h-56-px bg-neutral-50 radius-12"
                           placeholder="Email address"
                           required
                           value="{{ old('email') }}">
                </div>

                <div class="position-relative mb-20">
                    <div class="icon-field">
                        <span class="icon top-50 translate-middle-y">
                            <iconify-icon icon="solar:lock-password-outline"></iconify-icon>
                        </span>
                        <input type="password"
                               name="password"
                               id="password"
                               class="form-control h-56-px bg-neutral-50 radius-12"
                               placeholder="Password"
                               required>
                        <button type="button"
                                id="togglePassword"
                                class="toggle-password ri-eye-line cursor-pointer position-absolute end-0 top-50 translate-middle-y me-16 text-secondary-light"
                                data-toggle="#password"></button>
                    </div>
                </div>

                <div class="d-flex justify-content-between mb-32">
                    <div class="form-check style-check d-flex align-items-center">
                        <input class="form-check-input border border-neutral-300" type="checkbox" id="remember">
                        <label class="form-check-label ms-2" for="remember">Remember me</label>
                    </div>
                    <a href="javascript:void(0)" class="text-primary-600 fw-medium">Forgot Password?</a>
                </div>

                <button type="submit" class="btn btn-primary text-sm btn-sm px-12 py-16 w-100 radius-12 mb-32">
                    Log In
                </button>

                <div class="d-flex flex-wrap gap-2 mb-32">
                    <button type="button" class="btn btn-secondary" onclick="fillLogin('a@a','a')">Admin</button>
                    {{-- <button type="button" class="btn btn-secondary" onclick="fillLogin('u@u','a')">User</button> --}}
                </div>

                <p class="text-center text-sm mb-0">
                    Don’t have an account?
                    <a href="{{ route('registerform') }}" class="text-primary-600 fw-semibold">Sign Up</a>
                </p>
            </form>
        </div>
    </div>
</section>

<!-- jQuery -->
<script src="{{ asset('assets/js/lib/jquery-3.7.1.min.js') }}"></script>
<!-- Bootstrap -->
<script src="{{ asset('assets/js/lib/bootstrap.bundle.min.js') }}"></script>
<!-- Apex Charts -->
<script src="{{ asset('assets/js/lib/apexcharts.min.js') }}"></script>
<!-- DataTables -->
<script src="{{ asset('assets/js/lib/dataTables.min.js') }}"></script>
<!-- Iconify -->
<script src="{{ asset('assets/js/lib/iconify-icon.min.js') }}"></script>
<!-- jQuery UI -->
<script src="{{ asset('assets/js/lib/jquery-ui.min.js') }}"></script>
<!-- Vector Map -->
<script src="{{ asset('assets/js/lib/jquery-jvectormap-2.0.5.min.js') }}"></script>
<script src="{{ asset('assets/js/lib/jquery-jvectormap-world-mill-en.js') }}"></script>
<!-- Popup -->
<script src="{{ asset('assets/js/lib/magnific-popup.min.js') }}"></script>
<!-- Slick Slider -->
<script src="{{ asset('assets/js/lib/slick.min.js') }}"></script>
<!-- Prism -->
<script src="{{ asset('assets/js/lib/prism.js') }}"></script>
<!-- File Upload -->
<script src="{{ asset('assets/js/lib/file-upload.js') }}"></script>
<!-- Audioplayer -->
<script src="{{ asset('assets/js/lib/audioplayer.js') }}"></script>
<!-- Main JS -->
<script src="{{ asset('assets/js/app.js') }}"></script>

<script>
    function fillLogin(email, password) {
        document.getElementById('emailaddress').value = email;
        document.getElementById('password').value = password;
        document.getElementById('loginForm').submit();
    }

    document.getElementById('togglePassword').addEventListener('click', function() {
        this.classList.toggle('ri-eye-off-line');
        let input = document.querySelector(this.getAttribute('data-toggle'));
        input.type = input.type === 'password' ? 'text' : 'password';
    });
</script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Register')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Animate.css for fade-in effects -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap 5 JavaScript Bundle (REQUIRED for dropdowns to work) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<title>
    @yield('title', 'Register')
</title>

<style>
    body {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        min-height: 100vh;
        color: #ffffff;
    }

    .card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(123, 143, 161, 0.2);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }

    .form-control:focus {
        border-color: #7b8fa1;
        box-shadow: 0 0 0 0.2rem rgba(123, 143, 161, 0.25);
    }

    .btn-primary {
        background: linear-gradient(45deg, #7b8fa1, #5a6c7d);
        border: none;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background: linear-gradient(45deg, #5a6c7d, #4a5d6a);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(123, 143, 161, 0.4);
    }

    .text-primary {
        color: #7b8fa1 !important;
    }

    .text-info {
        color: #5a6c7d !important;
    }

    /* Alert styling */
    .alert {
        border: none;
        border-radius: 10px;
        backdrop-filter: blur(10px);
    }

    /* Form animations */
    .form-floating {
        animation: fadeInUp 0.6s ease-out;
        animation-fill-mode: both;
    }

    .form-floating:nth-child(1) { animation-delay: 0.1s; }
    .form-floating:nth-child(2) { animation-delay: 0.2s; }
    .form-floating:nth-child(3) { animation-delay: 0.3s; }
    .form-floating:nth-child(4) { animation-delay: 0.4s; }
    .form-floating:nth-child(5) { animation-delay: 0.5s; }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Card entrance animation */
    .card {
        animation: cardEntrance 0.8s ease-out;
    }

    @keyframes cardEntrance {
        from {
            opacity: 0;
            transform: translateY(20px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
</style>

<body>
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="col-md-6">
            <div class="card shadow-lg p-5 border-0" style="background-color: #ffffff; border-radius: 20px;">
                <div class="text-center mb-4">
                    <h2 class="fw-bold text-primary">Sign up</h2>
                    <p class="text-muted">Enter details to register</p>
                </div>
                @if (session('success'))
                    <div id="success-alert" style="color: green; padding: 10px; border: 1px solid green; margin-bottom: 10px; position: relative;">
                        <button onclick="document.getElementById('success-alert').style.display='none'" 
                                style="position: absolute; right: 10px; top: 5px; border: none; background: transparent; font-weight: bold; font-size: 16px; cursor: pointer;">
                            &times;
                        </button>
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div id="error-alert" style="color: red; padding: 10px; border: 1px solid red; margin-bottom: 10px; position: relative;">
                        <button onclick="document.getElementById('error-alert').style.display='none'" 
                                style="position: absolute; right: 10px; top: 5px; border: none; background: transparent; font-weight: bold; font-size: 16px; cursor: pointer;">
                            &times;
                        </button>
                        <ul style="margin: 0;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Name</label>
                        <input type="text" class="form-control" name="name" id="name" required placeholder="Enter your name">
                    </div>

                    <div class="mb-3">
                        <label for="username" class="form-label fw-semibold">Username</label>
                        <input type="text" class="form-control" name="username" id="username" required placeholder="Enter your email">
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label fw-semibold">Role</label>
                        <select class="form-select" name="role" id="role" required>
                            <option value="" disabled selected>Select your role</option>
                            <option value="Management">Management</option>
                            <option value="Agent">Agent</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label fw-semibold">Password</label>
                        <input type="password" class="form-control" name="password" id="password" required placeholder="Enter your password">
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label fw-semibold">Confirm Password</label>
                        <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" required placeholder="Confirm your password">
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill">Sign up</button>
                    </div>

                    <div class="text-center mt-3">
                        <a href="{{ route('login') }}" class="text-decoration-none text-info">Already have an account</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

<footer class="text-center py-4 bg-dark text-white mt-5">
    <p class="mb-0">&copy; {{ date('Y') }} PEL Portal. All rights reserved.</p>
</footer>
</html>
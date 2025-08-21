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
        background: linear-gradient(to right, #00c6ff, #0072ff); /* Full page gradient background */
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
@extends('layouts.app')

@section('title', 'Home')

@section('content')

<section class="hero-section text-white bg-gradient-primary py-5" style="background: linear-gradient(135deg, #007BFF, #6610f2);">
    <div class="container text-center">
        <h1 class="display-4 fw-bold mb-3 animate__animated animate__fadeInDown">Welcome to the PEL Project Portal</h1>
        <p class="lead animate__animated animate__fadeInUp">Simplify project tracking, reporting, and collaboration — all in one place.</p>
        <a href="{{ route('login') }}" class="btn btn-light btn-lg mt-4 px-5 animate__animated animate__fadeInUp">Login</a>
    </div>
</section>

<!-- 💡 Features Section -->
<section class="features-section py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Why Choose PEL</h2>
            <p class="text-muted">Everything you need to streamline your workflow.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card border-0 shadow h-100 hover-zoom">
                    <div class="card-body text-center">
                        <i class="bi bi-shield-lock fs-1 text-primary mb-3"></i>
                        <h5 class="card-title">Secure Login</h5>
                        <p class="card-text">Robust authentication and session handling with Laravel security features.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow h-100 hover-zoom">
                    <div class="card-body text-center">
                        <i class="bi bi-bar-chart-line fs-1 text-success mb-3"></i>
                        <h5 class="card-title">Real-Time Reports</h5>
                        <p class="card-text">Live updates on activities, performance, and project milestones.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow h-100 hover-zoom">
                    <div class="card-body text-center">
                        <i class="bi bi-layout-text-window-reverse fs-1 text-info mb-3"></i>
                        <h5 class="card-title">User-Friendly UI</h5>
                        <p class="card-text">Clean, mobile-friendly design that's intuitive and fast to navigate.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 📊 Stats Section -->
<section class="stats-section py-5 bg-white">
    <div class="container text-center">
        <h2 class="mb-4 fw-bold">Project Insights</h2>
        <div class="row">
            <div class="col-md-3">
                <h3 class="text-primary fw-bold">25+</h3>
                <p>Active Users</p>
            </div>
            <div class="col-md-3">
                <h3 class="text-success fw-bold">100+</h3>
                <p>Projects Managed</p>
            </div>
            <div class="col-md-3">
                <h3 class="text-danger fw-bold">50+</h3>
                <p>Reports Generated</p>
            </div>
            <div class="col-md-3">
                <h3 class="text-warning fw-bold">5</h3>
                <p>Admin Users</p>
            </div>
        </div>
    </div>
</section>

<!-- ⚫ Footer -->
<footer class="text-center py-4 bg-dark text-white mt-5">
    <p class="mb-0">&copy; {{ date('Y') }} PEL Project Portal. All rights reserved.</p>
</footer>

@endsection

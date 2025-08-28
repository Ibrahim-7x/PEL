@extends('layouts.app')

@section('content')
<style>
    .welcome-hero {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        min-height: 60vh;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .welcome-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        opacity: 0.3;
    }

    .hero-content {
        position: relative;
        z-index: 2;
        animation: fadeInUp 1s ease-out;
    }

    .hero-title {
        font-size: 3.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
        text-shadow: 0 4px 8px rgba(0,0,0,0.3);
        background: linear-gradient(45deg, #7b8fa1, #5a6c7d);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .hero-subtitle {
        font-size: 1.2rem;
        margin-bottom: 2rem;
        opacity: 0.9;
    }

    .feature-cards {
        padding: 4rem 0;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }

    .feature-card {
        background: rgba(123, 143, 161, 0.05);
        backdrop-filter: blur(10px);
        border-radius: 15px;
        padding: 2rem;
        text-align: center;
        transition: all 0.3s ease;
        border: 1px solid rgba(123, 143, 161, 0.2);
        box-shadow: 0 4px 20px rgba(123, 143, 161, 0.1);
        animation: fadeInUp 0.8s ease-out;
        animation-fill-mode: both;
    }

    .feature-card:nth-child(1) { animation-delay: 0.2s; }
    .feature-card:nth-child(2) { animation-delay: 0.4s; }
    .feature-card:nth-child(3) { animation-delay: 0.6s; }

    .feature-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 8px 25px rgba(123, 143, 161, 0.2);
    }

    .feature-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(45deg, #7b8fa1, #5a6c7d);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        color: white;
        font-size: 2rem;
    }

    .stats-section {
        background: linear-gradient(135deg, #34495e 0%, #2c3e50 100%);
        color: white;
        padding: 4rem 0;
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        color: #7b8fa1;
        margin-bottom: 0.5rem;
    }

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

    @media (max-width: 768px) {
        .hero-title {
            font-size: 2.5rem;
        }
        .hero-subtitle {
            font-size: 1rem;
        }
    }
</style>

<div class="welcome-hero">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title">Welcome to PEL-Abacus</h1>
            <p class="hero-subtitle">Your comprehensive solution for customer relationship management and case tracking</p>
            <a href="{{ auth()->check() ? (auth()->user()->role === 'Agent' ? url('/home-agent') : url('/home-management')) : route('login') }}"
               class="btn btn-primary btn-lg px-5 py-3 rounded-pill">
                Get Started <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</div>

<div class="feature-cards">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-clipboard-check"></i>
                    </div>
                    <h4>RU Case Management</h4>
                    <p>Streamlined case tracking and management for efficient customer service operations.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-chat-dots"></i>
                    </div>
                    <h4>Real-time Communication</h4>
                    <p>Integrated chat system for seamless communication between agents and management.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-graph-up"></i>
                    </div>
                    <h4>Analytics & Reports</h4>
                    <p>Comprehensive reporting tools to track performance and customer satisfaction.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="stats-section">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3">
                <div class="stat-number">24/7</div>
                <div>Support Available</div>
            </div>
            <div class="col-md-3">
                <div class="stat-number">100%</div>
                <div>Secure & Reliable</div>
            </div>
            <div class="col-md-3">
                <div class="stat-number">Fast</div>
                <div>Response Time</div>
            </div>
            <div class="col-md-3">
                <div class="stat-number">Easy</div>
                <div>User Interface</div>
            </div>
        </div>
    </div>
</div>
@endsection
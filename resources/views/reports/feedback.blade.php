@extends('layouts.app')

@section('title', 'Feedback Export')

@section('content')
<style>
    .export-hero {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        min-height: 30vh;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        text-align: center;
        margin: -2rem -2rem 3rem -2rem;
        position: relative;
        overflow: hidden;
    }

    .export-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        opacity: 0.3;
    }

    .export-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
        text-shadow: 0 4px 8px rgba(0,0,0,0.3);
        background: linear-gradient(45deg, #7b8fa1, #5a6c7d);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .export-form-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(123, 143, 161, 0.2);
        border-radius: 15px;
        padding: 3rem;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        animation: fadeInUp 0.8s ease-out;
    }

    .form-group {
        margin-bottom: 2rem;
        animation: fadeInUp 0.6s ease-out;
        animation-fill-mode: both;
    }

    .form-group:nth-child(1) { animation-delay: 0.2s; }
    .form-group:nth-child(2) { animation-delay: 0.4s; }
    .form-group:nth-child(3) { animation-delay: 0.6s; }

    .form-label {
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 0.5rem;
        font-size: 1.1rem;
    }

    .form-control {
        border: 2px solid rgba(123, 143, 161, 0.2);
        border-radius: 10px;
        padding: 0.8rem 1rem;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: rgba(255, 255, 255, 0.8);
    }

    .form-control:focus {
        border-color: #7b8fa1;
        box-shadow: 0 0 0 0.2rem rgba(123, 143, 161, 0.25);
        background: white;
        transform: scale(1.02);
    }

    .btn-export {
        background: linear-gradient(45deg, #7b8fa1, #5a6c7d);
        border: none;
        border-radius: 25px;
        padding: 1rem 3rem;
        font-size: 1.1rem;
        font-weight: 600;
        color: white;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(123, 143, 161, 0.3);
        animation: fadeInUp 0.8s ease-out;
        animation-delay: 0.8s;
        animation-fill-mode: both;
    }

    .btn-export:hover {
        background: linear-gradient(45deg, #5a6c7d, #4a5d6a);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(123, 143, 161, 0.4);
        color: white;
    }

    .export-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(45deg, #7b8fa1, #5a6c7d);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        color: white;
        font-size: 1.5rem;
    }

    .export-description {
        color: rgba(255, 255, 255, 0.8);
        font-size: 1.1rem;
        margin-bottom: 2rem;
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
        .export-title {
            font-size: 2rem;
        }
        .export-form-card {
            padding: 2rem;
        }
    }
</style>

<div class="export-hero">
    <div class="container">
        <div class="text-center">
            <div class="export-icon">
                <i class="bi bi-file-earmark-spreadsheet"></i>
            </div>
            <h1 class="export-title">Feedback Export</h1>
            <p class="export-description">Generate comprehensive feedback reports for your selected date range</p>
        </div>
    </div>
</div>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="export-form-card">
                <form method="POST" action="{{ route('export.feedback.download') }}">
                    @csrf
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="bi bi-calendar-event me-2"></i>Start Date
                                </label>
                                <input type="date" name="start_date" class="form-control" required>
                                <small class="text-muted">Select the beginning date for the report</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="bi bi-calendar-check me-2"></i>End Date
                                </label>
                                <input type="date" name="end_date" class="form-control" required>
                                <small class="text-muted">Select the ending date for the report</small>
                            </div>
                        </div>
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-export">
                                <i class="bi bi-download me-2"></i>Generate & Export Report
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('title', 'Sign Up')

@section('content')
<style>
    body {
        background: linear-gradient(to right, #00c6ff, #0072ff); /* Full page gradient background */
    }
</style>
<div class="container d-flex justify-content-center align-items-center" style="min-height: 90vh;">
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
                    <label for="email" class="form-label fw-semibold">Email</label>
                    <input type="email" class="form-control" name="email" id="email" required placeholder="Enter your email">
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
@endsection

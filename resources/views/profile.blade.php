@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <!-- Profile Card -->
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                <!-- Header with Gradient -->
                <div class="card-header text-white p-4" style="background: linear-gradient(135deg, #007bff, #6610f2);">
                    <div class="d-flex align-items-center">
                        <!-- Avatar -->
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=random&size=100" 
                             alt="Avatar" 
                             class="rounded-circle border border-3 border-white me-3" 
                             width="80" height="80">
                        <div>
                            <h4 class="mb-0 fw-bold">{{ Auth::user()->name }}</h4>
                            <small class="text-light">{{ Auth::user()->username }}</small>
                        </div>
                    </div>
                </div>
                <!-- Body -->
                <div class="card-body bg-light">
                    <h5 class="fw-bold mb-3">Profile Information</h5>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="p-3 bg-white rounded shadow-sm d-flex align-items-center">
                                <span class="me-2 fs-4 text-primary">👤</span>
                                <div>
                                    <div class="fw-bold small text-muted">Full Name</div>
                                    <div>{{ Auth::user()->name }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mt-3 mt-md-0">
                            <div class="p-3 bg-white rounded shadow-sm d-flex align-items-center">
                                <span class="me-2 fs-4 text-success">📧</span>
                                <div>
                                    <div class="fw-bold small text-muted">username</div>
                                    <div>{{ Auth::user()->username }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex justify-content-end mt-4">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-danger">
                                🚪 Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

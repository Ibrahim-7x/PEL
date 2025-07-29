@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">My Profile</h5>
                    <a href="#" class="btn btn-sm btn-light">Edit Profile</a> {{-- You can later link this to a form --}}
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Name</label>
                        <div class="form-control-plaintext">{{ Auth::user()->name }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <div class="form-control-plaintext">{{ Auth::user()->email }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

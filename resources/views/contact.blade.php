@extends('layouts.app')

@section('title', 'Contact Us - PEL')

@section('content')
<div class="container mt-5 mb-5">
    <div class="text-center mb-5">
        <h1 class="display-5 fw-bold text-primary">Contact Us</h1>
        <p class="lead">We’re here to help — reach out to us using any of the following options.</p>
    </div>

    <div class="row mb-5">
        <div class="col-md-6">
            <h4 class="fw-bold">Corporate Office (Lahore)</h4>
            <p>17-Aziz Avenue, Canal Bank Road, Gulberg V, Lahore</p>
            <p><strong>UAN:</strong> 042-111-102-103<br><strong>Phone:</strong> 042-35811951</p>

            <h4 class="fw-bold mt-4">Regional Offices</h4>
            <p><strong>Karachi:</strong> 15-C, Lane 11, Main Khayaban-e-Ittehad, Phase 2 Ext., DHA<br>
            <strong>Phone:</strong> 021-35314061</p>
            <p><strong>Islamabad:</strong> Plot No. 82, St. 3, I-9/2 Industrial Area<br>
            <strong>Phone:</strong> 051-4446952</p>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h4 class="card-title mb-4">Send a Message</h4>

                    {{-- Success Message --}}
                    @if (session('contact_success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('contact_success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Validation Errors --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('contact') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Your Name</label>
                            <input type="text" name="name" id="name" class="form-control" required value="{{ old('name') }}">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" name="email" id="email" class="form-control" required value="{{ old('email') }}">
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Your Message</label>
                            <textarea name="message" id="message" rows="4" class="form-control" required>{{ old('message') }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg">Submit</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

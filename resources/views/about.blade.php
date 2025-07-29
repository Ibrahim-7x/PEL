@extends('layouts.app')

@section('title', 'About Us - PEL')

@section('content')
<div class="container mt-5">
    <div class="text-center mb-4">  
        <h1 class="display-5 fw-bold text-primary">About PEL</h1>
        <p class="lead">Pioneering Pakistan’s Power & Appliance Industry Since 1956</p>
    </div>

    <!-- Factory or Team Image -->
    <div class="row align-items-center mb-5">
        <div class="col-md-6">
            <img src="{{ asset('images/pel-about-us-banner.jpg') }}" alt="PEL Factory" class="img-fluid rounded shadow">
        </div>
        <div class="col-md-6">
            <h3 class="fw-bold">Our Heritage</h3>
            <p>
                Founded in 1956 and based in Lahore, Pak Elektron Limited (PEL) began in technical collaboration with AEG Germany.
                Today, as part of the Saigol Group, PEL leads in both electrical infrastructure and consumer appliances.
            </p>
        </div>
    </div>

    <!-- Production Line Image -->
    <div class="row align-items-center mb-5 flex-md-row-reverse">
        <div class="col-md-6">
            <img src="{{ asset('images/refrigerators-top-banner.jpg') }}" alt="PEL Factory" class="img-fluid rounded shadow">
        </div>
        <div class="col-md-6">
            <h3 class="fw-bold">What We Create</h3>
            <p>
                From energy meters and transformers to refrigerators, ACs, and water dispensers — PEL’s dual segments ensure excellence in power and appliances.
            </p>
        </div>
    </div>

    <!-- Industry or Transformer Image -->
    <div class="row mb-5">
        <div class="col-md-12 text-center">
            <img src="{{ asset('images/PEL_Trasformer.jpg') }}" alt="PEL Factory" class="img-fluid rounded shadow">
            <h3 class="fw-bold">Power & Energy Innovation</h3>
            <p>
                PEL shapes the power sector in Pakistan with reliable manufacturing and export of transformers and switchgear to global markets.
            </p>
        </div>
    </div>

    <!-- Team or CSR Image -->
    <div class="row mb-5">
        <div class="col-md-6">
            <img src="{{ asset('images/pel-earth-hour-event.jpg') }}" alt="PEL Team & CSR" class="img-fluid rounded shadow">
        </div>
        <div class="col-md-6">
            <h3 class="fw-bold">Our People & Values</h3>
            <p>
                At PEL, teamwork, corporate social responsibility (CSR), integrity, and innovation are the cornerstones of everything we do.
                We foster a collaborative environment where ideas are shared, talents are nurtured, and goals are achieved collectively. 
                Our culture is deeply rooted in ethical business practices, respect for our people, and a commitment to making a positive impact on society. 
                Through continuous innovation, we strive to offer cutting-edge solutions that improve lives and empower communities.
                Sustainability and excellence aren’t just values — they are our long-term promises to customers, employees, and future generations.
            </p>
        </div>
    </div>
</div>

<footer class="text-center py-4 bg-dark text-white mt-5">
    <p class="mb-0">&copy; {{ date('Y') }} PEL Project Portal. All rights reserved.</p>
</footer>

@endsection
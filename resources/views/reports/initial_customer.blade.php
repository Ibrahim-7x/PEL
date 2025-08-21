@extends('layouts.app')

@section('title', 'Export Initial Customer')

@section('content')
<h3>Export Initial Customer Information</h3>
<form method="POST" action="{{ route('export.initial_customer.download') }}">
    @csrf
    <div class="row">
        <div class="col-md-4">
            <label>Start Date</label>
            <input type="date" name="start_date" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label>End Date</label>
            <input type="date" name="end_date" class="form-control" required>
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-success">Export</button>
        </div>
    </div>
</form>
@endsection

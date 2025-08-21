@extends('layouts.app')

@section('title', 'Happy Call Export')

@section('content')
<h3>Export Happy Call Status</h3>
<form method="POST" action="{{ route('export.happy_call.download') }}">
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
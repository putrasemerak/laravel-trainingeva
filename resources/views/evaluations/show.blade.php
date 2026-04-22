@extends('layouts.app')

@section('title', 'View Evaluation - Training Effectiveness Evaluation')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">View Evaluation Record: {{ $evaluation->refnum }}</h6>
        <div>
            <a href="{{ route('evaluations.print', $evaluation->teid) }}" target="_blank" class="btn btn-sm btn-info">
                <i class="bi bi-printer"></i> Print
            </a>
            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-secondary">Back</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h5 class="font-weight-bold border-bottom pb-2">Personnel Information</h5>
                <table class="table table-sm table-borderless">
                    <tr><th width="40%">Ref No:</th><td>{{ $evaluation->refnum }}</td></tr>
                    <tr><th>Name:</th><td>{{ $evaluation->fullname }}</td></tr>
                    <tr><th>Emp No:</th><td>{{ $evaluation->empno }}</td></tr>
                    <tr><th>Division:</th><td>{{ $evaluation->div }}</td></tr>
                    <tr><th>Department:</th><td>{{ $evaluation->dept }}</td></tr>
                    <tr><th>Section:</th><td>{{ $evaluation->sec }}</td></tr>
                    <tr><th>Sub-Section:</th><td>{{ $evaluation->subsec }}</td></tr>
                    <tr><th>Unit:</th><td>{{ $evaluation->unit }}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h5 class="font-weight-bold border-bottom pb-2">Training Information</h5>
                <table class="table table-sm table-borderless">
                    <tr><th width="40%">Category:</th><td>{{ $evaluation->tcategory }}</td></tr>
                    <tr><th>Topic:</th><td>{{ $evaluation->topic }}</td></tr>
                    <tr><th>Start Date:</th><td>{{ $evaluation->entryin }}</td></tr>
                    <tr><th>End Date:</th><td>{{ $evaluation->entryout }}</td></tr>
                    <tr><th>Duration:</th><td>{{ $evaluation->tduration }}</td></tr>
                    <tr><th>Result:</th><td>{{ $evaluation->tresult }}</td></tr>
                </table>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <h5 class="font-weight-bold border-bottom pb-2">Evaluation Information</h5>
                <table class="table table-sm table-borderless">
                    <tr><th width="40%">Status:</th><td>
                        <span class="badge badge-{{ $evaluation->status == 'Evaluated' ? 'success' : 'warning' }}">
                            {{ $evaluation->status }}
                        </span>
                    </td></tr>
                    <tr><th>Due Date:</th><td>{{ $evaluation->duedate }}</td></tr>
                    <tr><th>Evaluator:</th><td>{{ $evaluation->ename }} ({{ $evaluation->eemp }})</td></tr>
                    <tr><th>Methodology:</th><td>{{ $evaluation->radiocom }}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h5 class="font-weight-bold border-bottom pb-2">Rating Summary</h5>
                <table class="table table-sm table-borderless">
                    <tr><th width="40%">Total Rating:</th><td><strong>{{ $evaluation->totaleffective }} / 10</strong></td></tr>
                    <tr><th>Effectiveness:</th><td>
                        @if($evaluation->totaleffective >= 5)
                            <span class="text-success font-weight-bold">Effective</span>
                        @else
                            <span class="text-danger font-weight-bold">Not Effective</span>
                        @endif
                    </td></tr>
                    <tr><th>Evaluation Date:</th><td>{{ $evaluation->dtevaluate ?? 'N/A' }}</td></tr>
                </table>
            </div>
        </div>

        <div class="mt-4">
            <h5 class="font-weight-bold border-bottom pb-2">Detailed Ratings</h5>
            <div class="row">
                <div class="col-md-4">A. Knowledge: {{ $evaluation->range }}</div>
                <div class="col-md-4">B. Skill: {{ $evaluation->range2 }}</div>
                <div class="col-md-4">C. Delivery: {{ $evaluation->range3 }}</div>
                <div class="col-md-4">D. Initiative: {{ $evaluation->range4 }}</div>
                <div class="col-md-4">E. Reduction Error: {{ $evaluation->range5 }}</div>
                <div class="col-md-4">F. Work Quality: {{ $evaluation->range6 }}</div>
            </div>
        </div>

        <div class="mt-4">
            <h5 class="font-weight-bold border-bottom pb-2">Evaluator Comments</h5>
            <p class="bg-light p-3 border rounded">{{ $evaluation->evaluator ?: 'No comments provided.' }}</p>
        </div>

        <div class="mt-4">
            <h5 class="font-weight-bold border-bottom pb-2">HR Remarks</h5>
            <p class="bg-light p-3 border rounded">{{ $evaluation->remarkhr ?: 'No remarks.' }}</p>
        </div>
    </div>
</div>
@endsection

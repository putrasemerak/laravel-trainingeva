@extends('layouts.app')

@section('title', 'My Training Dashboard')

@section('content')
<div class="row">
    <!-- Summary Stats -->
    <div class="col-md-12 mb-4">
        <div class="card bg-primary text-white shadow">
            <div class="card-body py-3 d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="mb-0 font-weight-bold">My Training Summary</h5>
                    <div class="small opacity-75">Employee No: {{ auth()->user()->EmpNo }}</div>
                </div>
                <div class="text-right">
                    <span class="h4 mb-0 font-weight-bold">{{ $history->count() }}</span>
                    <div class="small opacity-75">Total Trainings</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Evaluation Requests -->
    <div class="{{ $pendingTasks->count() > 0 ? 'col-xl-6 col-lg-6' : 'col-xl-4 col-lg-5' }}">
        <div class="card shadow mb-4 border-left-warning">
            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-warning">My Evaluation Status</h6>
                <a href="{{ route('user.evaluations.create') }}" class="btn btn-xs btn-warning">New Request</a>
            </div>
            <div class="card-body p-0" style="height: 250px; overflow-y: auto;">
                <div class="list-group list-group-flush">
                    @forelse($myEvaluations as $eval)
                    <div class="list-group-item p-3">
                        <div class="d-flex w-100 justify-content-between mb-1">
                            <span class="font-weight-bold text-dark">{{ $eval->refnum }}</span>
                            <small class="badge badge-{{ $eval->status == 'Evaluated' ? 'success' : ($eval->status == 'Overdue' ? 'danger' : 'warning') }}">
                                {{ $eval->status }}
                            </small>
                        </div>
                        <p class="mb-1 small text-muted font-weight-bold">{{ $eval->topic }}</p>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <small class="text-muted"><i class="bi bi-person"></i> {{ $eval->ename }}</small>
                            @if($eval->status == 'Evaluated')
                                <small class="text-success font-weight-bold">Score: {{ $eval->totaleffective }}</small>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="h-100 d-flex flex-column align-items-center justify-content-center text-muted">
                        <i class="bi bi-clipboard-check h3 d-block mb-1"></i>
                        <span class="small">No active requests.</span>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @if($pendingTasks->count() > 0)
    <!-- Tasks for Evaluator/Supervisor -->
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4 border-left-success">
            <div class="card-header py-3 d-flex align-items-center justify-content-between bg-success text-white">
                <h6 class="m-0 font-weight-bold">Tasks to Evaluate (Supervisor)</h6>
                <span class="badge badge-light text-success">{{ $pendingTasks->count() }} PENDING</span>
            </div>
            <div class="card-body p-0" style="height: 250px; overflow-y: auto;">
                <div class="list-group list-group-flush">
                    @foreach($pendingTasks as $task)
                    <a href="{{ route('evaluations.evaluate', $task->teid) }}" class="list-group-item list-group-item-action p-3">
                        <div class="d-flex w-100 justify-content-between mb-1">
                            <span class="font-weight-bold text-primary">{{ $task->refnum }}</span>
                            <small class="text-{{ $task->status == 'Overdue' ? 'danger' : 'muted' }} font-weight-bold">
                                Due: {{ \Carbon\Carbon::parse($task->duedate)->format('d M') }}
                            </small>
                        </div>
                        <p class="mb-1 small text-dark font-weight-bold">{{ $task->fullname }}</p>
                        <div class="small text-muted text-truncate">{{ $task->topic }}</div>
                        <div class="mt-2 text-right">
                            <span class="btn btn-xs btn-outline-success">Evaluate Now <i class="bi bi-arrow-right-short"></i></span>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Training History -->
    <div class="{{ $pendingTasks->count() > 0 ? 'col-xl-12' : 'col-xl-8 col-lg-7' }}">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Training History (Legacy Records)</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover" id="historyTable">
                        <thead>
                            <tr>
                                <th>Training Topic</th>
                                <th>Date</th>
                                <th>How Long Ago</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($history as $record)
                            <tr>
                                <td class="font-weight-bold small">{{ $record->Title }}</td>
                                <td class="small">{{ \Carbon\Carbon::parse($record->TDate)->format('d M Y') }}</td>
                                <td class="small">
                                    @php
                                        $tdate = \Carbon\Carbon::parse($record->TDate);
                                        $diff = $tdate->diffForHumans(['parts' => 2]);
                                    @endphp
                                    {{ $diff }}
                                </td>
                                <td>
                                    <span class="badge badge-light border small">{{ $record->Status }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#historyTable').DataTable({
            "pageLength": 10,
            "order": [[ 1, "desc" ]]
        });
    });
</script>
@endsection

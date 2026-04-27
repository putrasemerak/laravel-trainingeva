@extends('layouts.app')

@section('title', 'Employee Training Notifications')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm main-card">
        <div class="card-header theme-card-header text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Employee Training Notifications (To Evaluate Tracking)</h4>
            <a href="{{ route('training.attendance') }}" class="btn btn-sm btn-light">
                <i class="bi bi-plus-circle"></i> New Attendance Entry
            </a>
        </div>
        <div class="card-body">
            <div class="alert alert-info py-2 small mb-4">
                <i class="bi bi-info-circle-fill"></i> This list shows all employees currently in the 3-month evaluation phase. 
                Records where the <strong>Due Date</strong> has passed or is today have already been automatically emailed to supervisors.
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover border theme-table" id="notificationsTable">
                    <thead class="theme-thead">
                        <tr>
                            <th>Ref No</th>
                            <th>Emp No</th>
                            <th>Employee Name</th>
                            <th class="allow-wrap">Training Topic</th>
                            <th>Training Date</th>
                            <th>Evaluation Due</th>
                            <th>Supervisor</th>
                            <th>Supervisor Email</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($notifications as $record)
                        <tr>
                            <td class="font-weight-bold">{{ $record->refnum }}</td>
                            <td>{{ $record->empno }}</td>
                            <td>{{ $record->fullname }}</td>
                            <td class="allow-wrap">{{ $record->topic }}</td>
                            <td>{{ $record->entryin }}</td>
                            <td>
                                @php
                                    $dueDate = \Carbon\Carbon::parse($record->duedate);
                                    $isPast = $dueDate->isPast();
                                    $isToday = $dueDate->isToday();
                                @endphp
                                <span class="{{ $isPast ? 'text-danger font-weight-bold' : ($isToday ? 'text-warning font-weight-bold' : '') }}">
                                    {{ $record->duedate }}
                                    @if($isPast && !$isToday)
                                        <br><small class="badge badge-danger">Notified</small>
                                    @elseif($isToday)
                                        <br><small class="badge badge-warning">Notifying Today</small>
                                    @else
                                        <br><small class="text-muted italic">Waiting 3 Months</small>
                                    @endif
                                </span>
                            </td>
                            <td>{{ $record->ename }}</td>
                            <td>{{ $record->eemail }}</td>
                            <td class="text-center">
                                <span class="badge badge-secondary">{{ $record->status }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .main-card {
        background-color: var(--bg-card) !important;
        border-color: var(--border-color) !important;
    }
    .theme-card-header {
        background-color: #4b2c71 !important; /* Professional Purple Header */
    }
    .theme-table {
        color: var(--text-body) !important;
        background-color: var(--bg-card) !important;
        border-color: var(--border-color) !important;
    }
    .theme-thead {
        background-color: var(--bg-navbar) !important;
        color: #fff !important;
    }
    .theme-thead th {
        border-color: var(--border-color) !important;
    }
    .table td {
        color: var(--text-body) !important;
        vertical-align: middle !important;
    }
    .italic { font-style: italic; }
</style>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#notificationsTable').DataTable({
            "order": [[ 5, "asc" ]],
            "pageLength": 25
        });
    });
</script>
@endsection

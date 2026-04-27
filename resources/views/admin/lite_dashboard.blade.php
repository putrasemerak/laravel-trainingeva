@extends('layouts.app')

@section('title', 'Admin Dashboard - Summary')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 font-weight-bold">System Overview</h4>
            <p class="text-muted mb-0">Welcome back, Admin. Here is a lite summary of the training status.</p>
        </div>
        <div class="text-end">
            <span class="badge badge-light border p-2">{{ date('l, d F Y') }}</span>
        </div>
    </div>

    <!-- Summary Cards Row -->
    <div class="row">
        <!-- 1. Total Trainings This Year -->
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm border-left-primary h-100 py-2 main-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Trainings ({{ date('Y') }})</div>
                            <div class="h3 mb-0 font-weight-bold">{{ $stats['total_trainings_year'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Upcoming Trainings (OPEN) -->
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm border-left-success h-100 py-2 main-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Upcoming (Open)</div>
                            <div class="h3 mb-0 font-weight-bold">{{ $stats['upcoming_trainings'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-door-open fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Un-evaluated Records -->
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm border-left-warning h-100 py-2 main-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Evaluations</div>
                            <div class="h3 mb-0 font-weight-bold">{{ $stats['pending_evaluations'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-hourglass-split fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Avg Effectiveness -->
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm border-left-info h-100 py-2 main-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Avg. Effectiveness</div>
                            <div class="h3 mb-0 font-weight-bold">{{ number_format($stats['avg_effectiveness'], 2) }} / 10</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-graph-up-arrow fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row: Quick Actions & Recent Activity Lite -->
    <div class="row mt-2">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100 main-card">
                <div class="card-header bg-transparent border-bottom-0 pt-3">
                    <h6 class="font-weight-bold text-primary">Quick Entry Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4 mb-3">
                            <a href="{{ route('training.attendance') }}" class="btn btn-outline-primary btn-block p-3">
                                <i class="bi bi-person-plus d-block h4 mb-1"></i>
                                <span class="small">Add Attendance</span>
                            </a>
                        </div>
                        <div class="col-4 mb-3">
                            <a href="{{ route('training.master_form') }}" class="btn btn-outline-success btn-block p-3">
                                <i class="bi bi-file-earmark-medical d-block h4 mb-1"></i>
                                <span class="small">Full Entry Form</span>
                            </a>
                        </div>
                        <div class="col-4 mb-3">
                            <a href="{{ route('training.notifications') }}" class="btn btn-outline-warning btn-block p-3">
                                <i class="bi bi-bell d-block h4 mb-1"></i>
                                <span class="small">Track Reminders</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100 main-card">
                <div class="card-header bg-transparent border-bottom-0 pt-3">
                    <h6 class="font-weight-bold text-primary">Evaluation Breakdown</h6>
                </div>
                <div class="card-body pt-0">
                    <div class="progress mb-3 mt-3" style="height: 25px;">
                        @php
                            $total = max($stats['total_evaluations'], 1);
                            $eval_pct = ($stats['completed_evaluations'] / $total) * 100;
                            $pend_pct = ($stats['pending_evaluations'] / $total) * 100;
                        @endphp
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $eval_pct }}%" title="Evaluated">{{ round($eval_pct) }}% Done</div>
                        <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $pend_pct }}%" title="Pending">{{ round($pend_pct) }}% Pending</div>
                    </div>
                    <ul class="list-group list-group-flush small">
                        @foreach($stats['status_breakdown'] as $s)
                        <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent theme-label py-1">
                            {{ $s->status }}
                            <span class="badge badge-secondary badge-pill">{{ $s->count }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Minimalistic Alert for Overdue -->
    @if($stats['overdue_count'] > 0)
    <div class="alert alert-danger shadow-sm border-0 d-flex align-items-center" role="alert">
        <i class="bi bi-exclamation-octagon-fill h4 mb-0 mr-3"></i>
        <div>
            There are <strong>{{ $stats['overdue_count'] }}</strong> overdue evaluations that require immediate attention.
            <a href="{{ route('evaluations') }}?status=Overdue" class="alert-link ml-2">Review them now &rarr;</a>
        </div>
    </div>
    @endif
</div>

<style>
    .border-left-primary { border-left: .25rem solid #4e73df !important; }
    .border-left-success { border-left: .25rem solid #1cc88a !important; }
    .border-left-info { border-left: .25rem solid #36b9cc !important; }
    .border-left-warning { border-left: .25rem solid #f6c23e !important; }
    .text-xs { font-size: .7rem; }
    .main-card { background-color: var(--bg-card) !important; border-color: var(--border-color) !important; color: var(--text-body) !important; }
    .theme-label { color: var(--text-body) !important; }
    .list-group-item { border-color: var(--border-color) !important; }
</style>
@endsection

@extends('layouts.app')

@section('title', 'Audit Trail - Admin')

@section('content')
<div class="header-bar mb-4 d-flex justify-content-between align-items-center">
    <h4 class="page-title"><i class="bi bi-clock-history mr-2 text-primary"></i>Audit Trail</h4>
    <a href="{{ route('dashboard') }}" class="btn btn-secondary shadow-sm">
        <i class="bi bi-arrow-left-circle"></i> Back to Dashboard
    </a>
</div>

<!-- Search and Date Filter -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.audit') }}" class="row align-items-end">
            <div class="col-md-4">
                <label class="font-weight-bold small">Search</label>
                <div class="input-group">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search user, action, page...">
                    @if(request('search'))
                        <div class="input-group-append">
                            <a href="{{ route('admin.audit') }}" class="btn btn-outline-secondary"><i class="bi bi-x"></i></a>
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-md-2">
                <label class="font-weight-bold small">From Date</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="font-weight-bold small">To Date</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Search</button>
                <a href="{{ route('admin.audit') }}" class="btn btn-outline-secondary">Clear</a>
            </div>
        </form>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex align-items-center">
        <div class="custom-control custom-checkbox mr-3">
            <input type="checkbox" id="select-all" class="custom-control-input">
            <label for="select-all" class="custom-control-label font-weight-bold">Select All</label>
        </div>
        <button type="button" id="btn-export" class="btn btn-success btn-sm shadow-sm" disabled>
            <i class="bi bi-download"></i> Export Selected
        </button>
    </div>
    <div class="text-muted small">Showing latest records ({{ $logs->total() }} total)</div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <form id="export-form" method="POST" action="{{ route('admin.audit.export') }}">
            @csrf
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th style="width: 40px;"></th>
                        <th>#</th>
                        <th>User ID</th>
                        <th>User Name</th>
                        <th>Action</th>
                        <th>Page</th>
                        <th>Description</th>
                        <th>Date</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="selected_records[]" value="{{ $log->ID }}" class="custom-control-input record-checkbox" id="check-{{ $log->ID }}">
                                <label class="custom-control-label" for="check-{{ $log->ID }}"></label>
                            </div>
                        </td>
                        <td>{{ ($logs->currentPage()-1) * $logs->perPage() + $loop->iteration }}</td>
                        <td>{{ $log->USER_ID }}</td>
                        <td>{{ $log->USER_NAME }}</td>
                        <td>
                            @php
                                $badgeClass = match (strtolower($log->ACTION_TYPE)) {
                                    'insert' => 'badge-success',
                                    'update' => 'badge-warning',
                                    'delete', 'cancel' => 'badge-danger',
                                    'view' => 'badge-info',
                                    'login' => 'badge-primary',
                                    default => 'badge-secondary'
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ strtoupper($log->ACTION_TYPE) }}</span>
                        </td>
                        <td>{{ $log->PAGE_NAME }}</td>
                        <td class="small">{{ $log->DESCRIPTION }}</td>
                        <td>{{ $log->ADDDATE }}</td>
                        <td>{{ $log->ADDTIME }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">No audit records found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </form>
    </div>
</div>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 pb-5">
    @if($logs->total() > 0)
    <div class="text-muted mb-3 mb-md-0">
        Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} of {{ $logs->total() }} results
    </div>
    @endif
    <div>
        {{ $logs->appends(request()->query())->links() }}
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#select-all').change(function() {
        $('.record-checkbox').prop('checked', $(this).prop('checked'));
        updateExportButton();
    });

    $('.record-checkbox').change(function() {
        updateExportButton();
        if(!$(this).prop('checked')) {
            $('#select-all').prop('checked', false);
        }
    });

    function updateExportButton() {
        var anyChecked = $('.record-checkbox:checked').length > 0;
        $('#btn-export').prop('disabled', !anyChecked);
    }

    $('#btn-export').click(function() {
        $('#export-form').submit();
    });
});
</script>
@endsection

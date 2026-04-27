@extends('layouts.app')

@section('title', 'Admin Dashboard - Training Effectiveness Evaluation')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">{{ __('ui.admin_dashboard') }}</h1>
    <div>
        <button type="button" id="btn-export-main" class="btn btn-sm btn-success shadow-sm mr-2" disabled>
            <i class="bi bi-download"></i> Export Selected
        </button>
    </div>
</div>

<!-- Content Row -->
<div class="row">
    @php
        $stats = [
            ['label' => 'Total Records', 'val' => count($evaluations), 'icon' => 'bi-files', 'border' => 'primary'],
            ['label' => 'Evaluated', 'val' => $evaluations->where('status', 'Evaluated')->count(), 'icon' => 'bi-check-circle', 'border' => 'success'],
            ['label' => 'Pending', 'val' => $evaluations->where('status', '!=', 'Evaluated')->count(), 'icon' => 'bi-clock-history', 'border' => 'warning'],
            ['label' => 'Synced Employees', 'val' => \App\Models\Employee::count(), 'icon' => 'bi-people', 'border' => 'info'],
        ];
    @endphp

    @foreach($stats as $stat)
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-{{ $stat['border'] }} shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-{{ $stat['border'] }} text-uppercase mb-1">{{ $stat['label'] }}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stat['val'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi {{ $stat['icon'] }} fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Status Tabs -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <ul class="nav nav-tabs card-header-tabs" id="statusTabs" role="tablist">
            @php $tabs = ['To Notify', 'To Evaluate', 'Evaluated', 'Overdue']; @endphp
            @foreach($tabs as $tab)
            <li class="nav-item">
                <a class="nav-link {{ $loop->first ? 'active' : '' }}" id="{{ Str::slug($tab) }}-tab" data-toggle="tab" href="#{{ Str::slug($tab) }}" role="tab">
                    {{ $tab }} 
                    <span class="badge badge-pill badge-secondary small ml-1">{{ $evaluations->where('status', $tab)->count() }}</span>
                </a>
            </li>
            @endforeach
        </ul>
    </div>
    <div class="card-body">
        <form id="export-form" method="POST" action="{{ route('evaluations.export') }}">
            @csrf
            <div class="tab-content" id="statusTabsContent">
                @foreach($tabs as $tab)
                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="{{ Str::slug($tab) }}" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover evaluationTable" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="width: 40px;"><input type="checkbox" class="select-all"></th>
                                    <th>Ref No</th>
                                    <th>Employee No</th>
                                    <th>Name</th>
                                    <th class="allow-wrap">Topic</th>
                                    <th>Training Date</th>
                                    <th>Result</th>
                                    <th>Evaluation Date</th>
                                    <th>Due Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($evaluations->where('status', $tab) as $eval)
                                <tr>
                                    <td><input type="checkbox" name="selected_records[]" value="{{ $eval->teid }}" class="record-checkbox"></td>
                                    <td class="font-weight-bold">{{ $eval->refnum }}</td>
                                    <td>{{ $eval->empno }}</td>
                                    <td>{{ $eval->fullname }}</td>
                                    <td class="allow-wrap">{{ $eval->topic }}</td>
                                    <td>{{ $eval->entryin }}</td>
                                    <td>
                                        <span class="text-{{ $eval->tresult == 'Passed' ? 'success' : ($eval->tresult == 'Failed' ? 'danger' : 'dark') }}">
                                            {{ $eval->tresult }}
                                        </span>
                                    </td>
                                    <td>{{ $eval->dtevaluate ?? '-' }}</td>
                                    <td class="{{ $tab == 'Overdue' ? 'text-danger font-weight-bold' : '' }}">
                                        {{ $eval->duedate }}
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('evaluations.show', $eval->teid) }}" class="btn btn-sm btn-info" title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('evaluations.evaluate', $eval->teid) }}" class="btn btn-sm btn-primary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="{{ route('evaluations.print', $eval->teid) }}" target="_blank" class="btn btn-sm btn-secondary" title="Print">
                                                <i class="bi bi-printer"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.evaluationTable').DataTable({
            "pageLength": 10,
            "order": [[ 1, "desc" ]],
            "columnDefs": [
                { "orderable": false, "targets": 0 }
            ]
        });

        // Handle Select All
        $('.select-all').change(function() {
            let isChecked = $(this).prop('checked');
            // Find checkboxes in the CURRENT active tab
            $(this).closest('table').find('.record-checkbox').prop('checked', isChecked);
            updateExportButton();
        });

        // Handle Individual Checkbox
        $(document).on('change', '.record-checkbox', function() {
            updateExportButton();
        });

        function updateExportButton() {
            let anyChecked = $('.record-checkbox:checked').length > 0;
            $('#btn-export-main').prop('disabled', !anyChecked);
        }

        $('#btn-export-main').click(function() {
            $('#export-form').submit();
        });
    });
</script>
@endsection

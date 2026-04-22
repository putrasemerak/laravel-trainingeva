@extends('layouts.app')

@section('title', 'Evaluator List - Training Effectiveness Evaluation')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">My Evaluation Tasks</h1>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <ul class="nav nav-tabs card-header-tabs" id="evaluatorTabs" role="tablist">
            @php $tabs = ['To Evaluate', 'Evaluated', 'Overdue']; @endphp
            @foreach($tabs as $tab)
            <li class="nav-item">
                <a class="nav-link {{ $loop->first ? 'active' : '' }}" id="eval-{{ Str::slug($tab) }}-tab" data-toggle="tab" href="#eval-{{ Str::slug($tab) }}" role="tab">
                    {{ $tab }}
                    <span class="badge badge-pill badge-secondary small ml-1">{{ $evaluations->where('status', $tab)->count() }}</span>
                </a>
            </li>
            @endforeach
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content" id="evaluatorTabsContent">
            @foreach($tabs as $tab)
            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="eval-{{ Str::slug($tab) }}" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover evaluationTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>Ref No</th>
                                <th>Participant Name</th>
                                <th>Emp No</th>
                                <th>Topic</th>
                                <th>Training Date</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($evaluations->where('status', $tab) as $evaluation)
                            <tr>
                                <td class="font-weight-bold">{{ $evaluation->refnum }}</td>
                                <td>{{ $evaluation->fullname }}</td>
                                <td>{{ $evaluation->empno }}</td>
                                <td class="small">{{ $evaluation->topic }}</td>
                                <td>{{ $evaluation->entryin }}</td>
                                <td class="{{ $tab == 'Overdue' ? 'text-danger font-weight-bold' : '' }}">
                                    {{ $evaluation->duedate }}
                                </td>
                                <td>
                                    <span class="badge badge-{{ $evaluation->status == 'Evaluated' ? 'success' : ($evaluation->status == 'Overdue' ? 'danger' : 'warning') }}">
                                        {{ $evaluation->status }}
                                    </span>
                                </td>
                                <td>
                                    @if($evaluation->status != 'Evaluated')
                                        <a href="{{ route('evaluations.evaluate', $evaluation->teid) }}" class="btn btn-sm btn-success">
                                            <i class="bi bi-pencil-square"></i> Evaluate Now
                                        </a>
                                    @else
                                        <a href="{{ route('evaluations.print', $evaluation->teid) }}" target="_blank" class="btn btn-sm btn-info">
                                            <i class="bi bi-printer"></i> Print
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.evaluationTable').DataTable({
            "pageLength": 10,
            "order": [[ 0, "desc" ]]
        });
    });
</script>
@endsection

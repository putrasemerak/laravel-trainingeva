@extends('layouts.app')

@section('title', 'My Training Dashboard')

@section('styles')
<style>
    .table-hover tbody tr:hover {
        background-color: rgba(0,0,0,.03);
    }
</style>
@endsection

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
                <div>
                    @if(auth()->user()->isSuperUser())
                        <a href="{{ route('training.notifications') }}" class="btn btn-xs btn-primary mr-2">Employee Training Notifications</a>
                    @endif
                </div>
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
                                <button type="button" class="btn btn-xs btn-outline-info view-eval-details" data-id="{{ $eval->teid }}">Details</button>
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
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($history as $record)
                            <tr>
                                <td class="font-weight-bold small">
                                    <a href="javascript:void(0)" class="view-training-detail text-primary" 
                                       data-empno="{{ $record->EmpNo }}" 
                                       data-title="{{ $record->Title }}" 
                                       data-tdate="{{ $record->TDate }}">
                                        {{ $record->Title }}
                                    </a>
                                </td>
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
                                <td>
                                    @php
                                        // Try to find if this training has an evaluation
                                        $eval = $myEvaluations->where('topic', $record->Title)->where('status', 'Evaluated')->first();
                                    @endphp
                                    @if($eval)
                                        <button type="button" class="btn btn-xs btn-primary view-eval-details" data-id="{{ $eval->teid }}">
                                            <i class="bi bi-eye"></i> View Eval
                                        </button>
                                    @else
                                        <span class="text-muted small italic">No Eval</span>
                                    @endif
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

<!-- Training Detail Modal -->
<div class="modal fade" id="trainingDetailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Training Details</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div id="train-loading" class="text-center py-4">
                    <div class="spinner-border text-info"></div>
                    <div class="mt-2">Loading training info...</div>
                </div>
                <div id="train-content" style="display:none; color: #000000 !important;">
                    <div class="mb-3 border-bottom pb-2">
                        <label class="small text-muted mb-0">Training Topic</label>
                        <div id="train-topic" class="font-weight-bold" style="color: #000 !important;"></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="small text-muted mb-0">Date</label>
                            <div id="train-date" class="font-weight-bold" style="color: #000 !important;"></div>
                        </div>
                        <div class="col-6">
                            <label class="small text-muted mb-0">Duration</label>
                            <div id="train-duration" class="font-weight-bold" style="color: #000 !important;"></div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="small text-muted mb-0">Venue</label>
                            <div id="train-venue" class="font-weight-bold" style="color: #000 !important;"></div>
                        </div>
                        <div class="col-6">
                            <label class="small text-muted mb-0">Trainer</label>
                            <div id="train-trainer" class="font-weight-bold" style="color: #000 !important;"></div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="small text-muted mb-0">Category</label>
                            <div id="train-category" class="font-weight-bold" style="color: #000 !important;"></div>
                        </div>
                        <div class="col-6">
                            <label class="small text-muted mb-0">Status</label>
                            <div id="train-status" class="font-weight-bold" style="color: #000 !important;"></div>
                        </div>
                    </div>

                    <!-- Evaluation Data (TE_0001) -->
                    <div id="train-eval-section" style="display:none;" class="mt-3 pt-2 border-top">
                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="small text-muted mb-0">Training Result</label>
                                <div id="train-result" class="font-weight-bold" style="color: #000 !important;"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted mb-0">Evaluator Remark</label>
                            <div id="train-evaluator-remark" class="p-2 border rounded bg-light" style="color: #000 !important; min-height: 40px;"></div>
                        </div>
                        <div class="mb-0">
                            <label class="small text-muted mb-0">HR Comment</label>
                            <div id="train-hr-comment" class="p-2 border rounded bg-light" style="color: #000 !important; min-height: 40px;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Evaluation Detail Modal -->
<div class="modal fade" id="evalDetailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Evaluation Details</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div id="eval-loading" class="text-center py-4">
                    <div class="spinner-border text-primary"></div>
                    <div class="mt-2">Loading details...</div>
                </div>
                <div id="eval-content" style="display:none;">
                    <div class="mb-3 border-bottom pb-2">
                        <label class="small text-muted mb-0">Topic</label>
                        <div id="det-topic" class="font-weight-bold"></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="small text-muted mb-0">Evaluator</label>
                            <div id="det-evaluator" class="font-weight-bold"></div>
                        </div>
                        <div class="col-6 text-right">
                            <label class="small text-muted mb-0">Date Evaluated</label>
                            <div id="det-date" class="font-weight-bold"></div>
                        </div>
                    </div>
                    <div class="card bg-light mb-3">
                        <div class="card-body p-2">
                            <div class="row">
                                <div class="col-6">
                                    <label class="small text-muted mb-0">Overall Score</label>
                                    <div id="det-score" class="h4 font-weight-bold text-success mb-0"></div>
                                </div>
                                <div class="col-6 text-right">
                                    <label class="small text-muted mb-0">Result</label>
                                    <div id="det-result" class="font-weight-bold"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="small text-muted mb-0">Supervisor Remarks / Recommendations</label>
                        <div id="det-remarks" class="p-2 border rounded bg-white" style="min-height: 60px;"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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

        $('.view-eval-details').click(function() {
            const teid = $(this).data('id');
            $('#evalDetailModal').modal('show');
            $('#eval-loading').show();
            $('#eval-content').hide();

            $.get('/api/evaluations/' + teid, function(data) {
                $('#det-topic').text(data.topic);
                $('#det-evaluator').text(data.ename);
                $('#det-date').text(data.dtevaluate);
                $('#det-score').text(data.totaleffective + ' / 6');
                $('#det-result').text(data.tresult);
                $('#det-remarks').text(data.remarkhr || 'No remarks provided.');

                $('#eval-loading').hide();
                $('#eval-content').fadeIn();
            }).fail(function() {
                alert('Failed to load evaluation details.');
                $('#evalDetailModal').modal('hide');
            });
        });

        $('.view-training-detail').click(function() {
            const empno = $(this).data('empno');
            const title = $(this).data('title');
            const tdate = $(this).data('tdate');

            $('#trainingDetailModal').modal('show');
            $('#train-loading').show();
            $('#train-content').hide();
            $('#train-eval-section').hide();

            $.get('/api/training-detail', { empno: empno, title: title, tdate: tdate }, function(data) {
                $('#train-topic').text(data.Title);
                $('#train-date').text(data.TDate);
                $('#train-duration').text(data.Period || '-');
                $('#train-venue').text(data.Venue || '-');
                $('#train-trainer').text(data.Trainer || '-');
                $('#train-category').text(data.Category || '-');
                $('#train-status').text(data.Status || '-');

                // If Evaluation data (TE_0001) is available
                if (data.eval_data) {
                    $('#train-result').text(data.eval_data.tresult || '-');
                    $('#train-evaluator-remark').text(data.eval_data.evaluator || 'No remarks provided.');
                    $('#train-hr-comment').text(data.eval_data.remarkhr || 'No comments provided.');
                    $('#train-eval-section').show();
                }

                $('#train-loading').hide();
                $('#train-content').fadeIn();
            }).fail(function() {
                alert('Failed to load training details.');
                $('#trainingDetailModal').modal('hide');
            });
        });
    });
</script>
@endsection

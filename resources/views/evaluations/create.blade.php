@extends('layouts.app')

@section('title', 'Create Evaluation')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-9">
        <div class="card shadow-sm mb-4 border-bottom-primary">
            <div class="card-header bg-white py-3">
                <h6 class="card-title-compact text-primary">
                    <i class="bi bi-file-earmark-plus mr-2"></i> New Training Effectiveness Evaluation
                </h6>
            </div>
            
            <div class="card-body px-4 py-3">
                <form action="{{ route('evaluations.store') }}" method="POST" class="form-compact">
                    @csrf

                    <div class="section-title mt-0 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Initial Setup</div>

                    <div class="form-group row align-items-center">
                        <label class="col-sm-3 col-form-label">Date Issued</label>
                        <div class="col-sm-4">
                            <input id="dtissued" name="dtissued" type="date" required class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>

                    <div class="section-title text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Person2nel Information</div>
                    
                    <div class="form-group row align-items-center">
                        <label class="col-sm-3 col-form-label">Employee Number</label>
                        <div class="col-sm-4">
                            <div class="input-group input-group-sm">
                                <input type="text" id="empno_search" name="empno" class="form-control font-weight-bold" placeholder="e.g. C2535" required>
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="button" id="btn-retrieve">
                                        <i class="bi bi-search"></i> Retrieve
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-5 small" id="fetch-status"></div>
                    </div>

                    <div class="form-group row align-items-center">
                        <label class="col-sm-3 col-form-label">Full Name</label>
                        <div class="col-sm-9">
                            <input type="text" id="fullname" name="fullname" class="form-control form-control-sm font-weight-bold bg-light" readonly required>
                        </div>
                    </div>

                    <div class="form-group row align-items-center">
                        <label class="col-sm-3 col-form-label">Division</label>
                        <div class="col-sm-9">
                            <input type="text" id="div" name="div" class="form-control form-control-sm bg-light" readonly required>
                        </div>
                    </div>

                    <div class="form-group row align-items-center">
                        <label class="col-sm-3 col-form-label">Department</label>
                        <div class="col-sm-9">
                            <input type="text" id="dept" name="dept" class="form-control form-control-sm bg-light" readonly required>
                        </div>
                    </div>

                    <div class="section-title text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Training Selection (from HR_0020)</div>

                    <div class="form-group row align-items-center">
                        <label class="col-sm-3 col-form-label">Select Record</label>
                        <div class="col-sm-9">
                            <select id="training_selector" class="form-control form-control-sm">
                                <option value="">-- Please Retrieve Employee First --</option>
                            </select>
                            <small class="text-info" id="training-count"></small>
                        </div>
                    </div>

                    <div id="training-details-section" style="display:none">
                        <div class="section-title text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Training Information</div>

                        <div class="form-group row align-items-center">
                            <label class="col-sm-3 col-form-label">Category</label>
                            <div class="col-sm-4">
                                <input id="tcategory" name="radiocom2" type="text" class="form-control form-control-sm bg-light" readonly>
                            </div>
                        </div>

                        <div class="form-group row align-items-center">
                            <label class="col-sm-3 col-form-label">Training Topic</label>
                            <div class="col-sm-9">
                                <input id="topic" name="topic" type="text" class="form-control form-control-sm bg-light font-weight-bold" readonly>
                            </div>
                        </div>

                        <div class="form-group row align-items-center mb-0">
                            <label class="col-sm-3 col-form-label">Training Dates</label>
                            <div class="col-sm-9">
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="input-group input-group-sm mb-2">
                                            <div class="input-group-prepend"><span class="input-group-text">Start</span></div>
                                            <input id="entryin" name="entryin" type="date" required class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="input-group input-group-sm mb-2">
                                            <div class="input-group-prepend"><span class="input-group-text">End</span></div>
                                            <input id="entryout" name="entryout" type="date" required class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row align-items-center">
                            <label class="col-sm-3 col-form-label">Duration / Result</label>
                            <div class="col-sm-9">
                                <div class="row">
                                    <div class="col-sm-5">
                                        <input id="tduration" name="tduration" type="text" required class="form-control form-control-sm" placeholder="Duration">
                                    </div>
                                    <div class="col-sm-5">
                                        <input id="tresult" name="tresult" type="text" class="form-control form-control-sm" value="Passed">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row align-items-center">
                            <label class="col-sm-3 col-form-label">Methodology</label>
                            <div class="col-sm-6">
                                <select name="radiocom" class="form-control form-control-sm" required>
                                    <option value="On the job observation">On the job observation</option>
                                    <option value="Written or Practical Test">Written or Practical Test</option>
                                    <option value="Dummy Project">Dummy Project</option>
                                </select>
                            </div>
                        </div>

                        <div class="section-title text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Evaluation Setup</div>

                        <div class="form-group row align-items-center">
                            <label class="col-sm-3 col-form-label">Status / Due Date</label>
                            <div class="col-sm-9">
                                <div class="row">
                                    <div class="col-sm-5">
                                        <select name="status" id="status" class="form-control form-control-sm" required>
                                            <option value="To Evaluate">To Evaluate</option>
                                            <option value="To Notify">To Notify</option>
                                            <option value="Evaluated">Evaluated</option>
                                            <option value="Overdue">Overdue</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-5">
                                        <input id="duedate" name="duedate" type="date" required class="form-control form-control-sm">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row align-items-center">
                            <label class="col-sm-3 col-form-label text-primary">Evaluator</label>
                            <div class="col-sm-9">
                                <input id="ename" name="ename" type="text" required class="form-control form-control-sm bg-light font-weight-bold text-primary" readonly>
                                <input id="eemp" name="eemp" type="hidden">
                                <input id="eemail" name="eemail" type="hidden">
                                <small class="text-muted" id="evaluator-details"></small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">HR Remarks</label>
                            <div class="col-sm-9">
                                <textarea class="form-control form-control-sm" id="remarkhr" name="remarkhr" rows="2"></textarea>
                            </div>
                        </div>

                        <div class="mt-4 text-center">
                            <hr>
                            <button type="submit" class="btn btn-success btn-sm px-5 py-2 font-weight-bold">
                                <i class="bi bi-check-circle mr-1"></i> SUBMIT EVALUATION REQUEST
                            </button>
                            <a href="{{ route('dashboard') }}" class="btn btn-link btn-sm text-muted">Cancel</a>
                        </div>
                    </div>

                    <input type="hidden" name="sec" id="sec">
                    <input type="hidden" name="subsec" id="subsec">
                    <input type="hidden" name="unit" id="unit">
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let globalTrainingHistory = [];

$(document).ready(function() {
    $('#btn-retrieve').click(function() {
        let empno = $('#empno_search').val();
        if (!empno) return alert('Please enter employee number');
        
        $('#fetch-status').text('Searching...').removeClass('text-success text-danger');

        $.ajax({
            url: '/api/employees/' + empno,
            method: 'GET',
            success: function(data) {
                $('#fullname').val(data.name || data.empname);
                $('#div').val(data.division_name);
                $('#dept').val(data.department_name);
                $('#sec').val(data.section_name);
                $('#subsec').val(data.subsection_name);
                $('#unit').val(data.unit_name);
                
                $('#eemp').val(data.supercode);
                $('#ename').val(data.evaluator_name);
                $('#eemail').val(data.evaluator_email);
                $('#evaluator-details').text('ID: ' + data.supercode + ' | Email: ' + data.evaluator_email);

                let selector = $('#training_selector');
                selector.empty().append('<option value="">-- Select a Training Record --</option>');
                
                globalTrainingHistory = data.training_history;
                
                if (globalTrainingHistory && globalTrainingHistory.length > 0) {
                    globalTrainingHistory.forEach((item, index) => {
                        let date = item.TDate ? new Date(item.TDate).toLocaleDateString() : 'N/A';
                        selector.append(`<option value="${index}">${item.Title} (${date})</option>`);
                    });
                    $('#training-count').text(globalTrainingHistory.length + ' records found');
                } else {
                    selector.append('<option value="">No training records found</option>');
                    $('#training-count').text('0 records found');
                }
                
                $('#fetch-status').html('<span class="text-success"><i class="bi bi-check-circle"></i> Found</span>');
                $('#training-details-section').show();
            },
            error: function() {
                $('#fetch-status').html('<span class="text-danger"><i class="bi bi-x-circle"></i> Not Found</span>');
                $('#training-details-section').hide();
            }
        });
    });

    $('#training_selector').change(function() {
        let index = $(this).val();
        if (index === "") return;

        let record = globalTrainingHistory[index];
        
        $('#topic').val(record.Title);
        $('#tcategory').val(record.Category || 'Internal');
        $('#tduration').val(record.Period + ' ' + (record.NOD > 0 ? 'Days' : 'Hrs'));
        
        if (record.TDate) {
            let tDate = new Date(record.TDate);
            let dateStr = tDate.toISOString().split('T')[0];
            $('#entryin').val(dateStr);
            $('#entryout').val(dateStr);
            
            tDate.setMonth(tDate.getMonth() + 6);
            $('#duedate').val(tDate.toISOString().split('T')[0]);
        }
    });

    $('#entryout').change(function() {
        let endDate = new Date($(this).val());
        if (!isNaN(endDate)) {
            endDate.setMonth(endDate.getMonth() + 6);
            $('#duedate').val(endDate.toISOString().split('T')[0]);
        }
    });
});
</script>
@endsection

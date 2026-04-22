@extends('layouts.app')

@section('title', 'New Evaluation Request')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm mb-4 border-bottom-primary">
            <div class="card-header bg-white py-3">
                <h6 class="card-title-compact text-primary">
                    <i class="bi bi-file-earmark-text mr-2"></i> Training Evaluation Request
                </h6>
            </div>
            
            <div class="card-body px-4 py-3">
                <form action="{{ route('user.evaluations.store') }}" method="POST" class="form-compact">
                    @csrf

                    <div class="section-title mt-0 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Personnel Information</div>
                    
                    <div class="form-group row align-items-center">
                        <label class="col-sm-3 col-form-label">Employee Number</label>
                        <div class="col-sm-4">
                            <div class="input-group input-group-sm">
                                <input type="text" id="emp_no_input" name="empno" class="form-control font-weight-bold" placeholder="e.g. C2535" required>
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="button" id="btn-fetch-user">
                                        <i class="bi bi-search"></i>
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

                    <div class="form-group row align-items-center">
                        <label class="col-sm-3 col-form-label text-primary">Supervisor</label>
                        <div class="col-sm-9 font-weight-bold text-primary">
                            <input type="text" id="ename" name="ename" class="form-control form-control-sm bg-light text-primary font-weight-bold" readonly required>
                            <input type="hidden" id="eemp" name="eemp">
                            <input type="hidden" id="eemail" name="eemail">
                        </div>
                    </div>

                    <div class="section-title text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Training Details</div>

                    <div class="form-group row align-items-center">
                        <label class="col-sm-3 col-form-label">Training Topic</label>
                        <div class="col-sm-9">
                            <select name="topic" id="topic_select" class="form-control form-control-sm" required>
                                <option value="">-- Loading Topics --</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row align-items-center">
                        <label class="col-sm-3 col-form-label">Start Date</label>
                        <div class="col-sm-4">
                            <input type="date" name="entryin" id="entryin" class="form-control form-control-sm" required>
                        </div>
                    </div>

                    <div class="form-group row align-items-center">
                        <label class="col-sm-3 col-form-label">End Date</label>
                        <div class="col-sm-4">
                            <input type="date" name="entryout" id="entryout" class="form-control form-control-sm" required>
                        </div>
                    </div>

                    <div class="form-group row align-items-center">
                        <label class="col-sm-3 col-form-label">Duration</label>
                        <div class="col-sm-4">
                            <input type="text" name="tduration" class="form-control form-control-sm" placeholder="e.g. 2 Days" required>
                        </div>
                    </div>

                    <div class="alert alert-light border small mt-4 mb-4" style="background-color: #f8f9fc;">
                        <i class="bi bi-info-circle text-info"></i> After submission, your <strong>Supervisor ({{ __('ui.evaluator') }})</strong> will be notified to evaluate your training effectiveness.
                    </div>

                    <div class="text-center">
                        <hr>
                        <button type="submit" class="btn btn-primary btn-sm px-5 py-2 font-weight-bold">
                            <i class="bi bi-send mr-1"></i> SUBMIT REQUEST
                        </button>
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
$(document).ready(function() {
    // Load Topics
    $.get('{{ route("api.topics.index") }}', function(data) {
        let select = $('#topic_select');
        select.empty().append('<option value="">-- SELECT TRAINING TOPIC --</option>');
        data.forEach(function(item) {
            select.append(`<option value="${item.Topic}">${item.Topic}</option>`);
        });
    });

    // Fetch User Details
    $('#btn-fetch-user').click(function() {
        let empno = $('#emp_no_input').val();
        if(!empno) return alert('Please enter Employee Number');
        
        $('#fetch-status').text('Searching...').removeClass('text-success text-danger');

        $.ajax({
            url: '/api/employees/' + empno,
            method: 'GET',
            success: function(data) {
                // Fix: Accessing correctly based on the EmployeeController structure
                $('#fullname').val(data.name || data.empname);
                $('#div').val(data.division_name);
                $('#dept').val(data.department_name);
                $('#sec').val(data.section_name);
                $('#subsec').val(data.subsection_name);
                $('#unit').val(data.unit_name);
                $('#ename').val(data.evaluator_name);
                $('#eemp').val(data.supercode);
                $('#eemail').val(data.evaluator_email);
                $('#fetch-status').html('<span class="text-success"><i class="bi bi-check-circle"></i> User Found</span>');
            },
            error: function() {
                $('#fetch-status').html('<span class="text-danger"><i class="bi bi-x-circle"></i> Not Found</span>');
                alert('Employee record not found.');
                // Clear fields
                $('#fullname, #div, #dept, #ename').val('');
            }
        });
    });
});
</script>
@endsection

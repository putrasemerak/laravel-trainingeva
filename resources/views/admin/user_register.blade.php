@extends('layouts.app')

@section('title', 'User Registration')

@section('content')
<style>
    .btn-clear-custom {
        color: var(--text-muted);
        border-color: var(--border-color);
        transition: all 0.3s ease;
    }
    .btn-clear-custom:hover {
        background-color: transparent !important;
        color: #ff9f43 !important;
        border-color: #ff9f43 !important;
        box-shadow: 0 0 10px rgba(255, 159, 67, 0.4);
    }
</style>
<div class="row justify-content-center">
    <div class="col-md-9">
        
        <!-- Status Cards -->
        @if(session('success_card'))
        <div class="card bg-success text-white shadow mb-4">
            <div class="card-body d-flex align-items-center">
                <i class="bi bi-check-circle-fill h3 mb-0 mr-3"></i>
                <div>
                    <div class="font-weight-bold">Registration Successful</div>
                    <div class="small">{{ session('success_card') }}</div>
                </div>
                <button type="button" class="close ml-auto text-white" onclick="this.parentElement.parentElement.remove()">&times;</button>
            </div>
        </div>
        @endif

        @if(session('error_card'))
        <div class="card bg-danger text-white shadow mb-4">
            <div class="card-body d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill h3 mb-0 mr-3"></i>
                <div>
                    <div class="font-weight-bold" style="font-size: 14px;">Registration Failed</div>
                    <div class="font-weight-bold">{{ session('error_card') }}</div>
                </div>
                <button type="button" class="close ml-auto text-white" onclick="this.parentElement.parentElement.remove()">&times;</button>
            </div>
        </div>
        @endif

        @if(session('user_exists_data'))
        <div class="card shadow mb-4" style="background-color: #d1e7dd; border: 1px solid #a3cfbb; color: #084298;">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="bi bi-question-circle-fill h3 mb-0 mr-3 text-success"></i>
                    <div style="color: #0f5132;">
                        <div class="font-weight-bold h5 mb-1">User Already Registered</div>
                        <div class="font-weight-bold">Employee <strong>{{ session('user_exists_data')['emp_no'] }}</strong> is already in the system. Do you want to overwrite their current information and update their role?</div>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-top d-flex justify-content-end" style="border-top-color: #a3cfbb !important;">
                    <button type="button" class="btn btn-outline-success btn-sm mr-3 font-weight-bold" onclick="this.parentElement.parentElement.parentElement.remove()">CANCEL</button>
                    <form action="{{ route('admin.users.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="force_update" value="1">
                        @foreach(session('user_exists_data') as $key => $val)
                            @if($key !== '_token' && $key !== 'force_update')
                                <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                            @endif
                        @endforeach
                        <button type="submit" class="btn btn-success btn-sm font-weight-bold shadow-sm">
                            <i class="bi bi-arrow-repeat mr-1"></i> YES, OVERWRITE AND UPDATE
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endif

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center bg-white">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-person-plus-fill mr-2"></i> Register System Access
                </h6>
                <a href="{{ route('admin.settings') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Settings
                </a>
            </div>
            <div class="card-body px-4 py-4">
                
                <form action="{{ route('admin.users.store') }}" method="POST" id="reg-form">
                    @csrf
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="small font-weight-bold text-dark">Employee Number</label>
                            <div class="input-group">
                                <input type="text" name="emp_no" id="input_emp_no" class="form-control form-control-lg border-primary font-weight-bold" 
                                       placeholder="Enter ID (e.g. C2535)" required value="{{ old('emp_no') }}">
                                <div class="input-group-append">
                                    <button class="btn btn-primary px-4" type="button" id="btn-lookup">
                                        <i class="bi bi-search mr-1"></i> FETCH
                                    </button>
                                </div>
                            </div>
                            <div id="lookup-wrapper" class="mt-2" style="display: none;">
                                <small id="lookup-msg" class="form-text p-2 rounded font-weight-bold"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="small font-weight-bold text-dark text-uppercase">Assign System Role</label>
                            <select name="role" class="form-control form-control-lg border-warning font-weight-bold" required>
                                <option value="">-- Choose Role --</option>
                                <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User (Standard)</option>
                                <option value="evaluator" {{ old('role') == 'evaluator' ? 'selected' : '' }}>Evaluator (Superior)</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin (HR)</option>
                                <option value="superuser" {{ old('role') == 'superuser' ? 'selected' : '' }}>SuperUser (IT)</option>
                            </select>
                        </div>
                    </div>

                    <div class="section-title text-primary text-uppercase mb-3" style="font-size: 0.75rem; border-bottom: 1px solid var(--border-color); font-weight: 800 !important;">
                        Employee Information Details
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label class="small font-weight-bold">Full Name</label>
                                <input type="text" name="name" id="field_name" class="form-control bg-light" value="{{ old('name') }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold">Email Address</label>
                                <input type="email" name="email" id="field_email" class="form-control bg-light" value="{{ old('email') }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold">Division</label>
                                <input type="text" name="div" id="field_div" class="form-control bg-light" value="{{ old('div') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold">Department</label>
                                <input type="text" name="dept" id="field_dept" class="form-control bg-light" value="{{ old('dept') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold">Position</label>
                                <input type="text" name="position" id="field_post" class="form-control bg-light" value="{{ old('position') }}">
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-light border mt-3 small text-muted">
                        <i class="bi bi-info-circle mr-1"></i> Default login password for new users will be their <strong>Employee Number</strong>.
                    </div>

                    <div class="mt-4 pt-3 border-top text-right">
                        <button type="reset" id="btn-reset-form" class="btn btn-clear-custom btn-sm mr-3">Clear Form</button>
                        <button type="submit" class="btn btn-success px-5 py-2 font-weight-bold shadow-sm">
                            <i class="bi bi-person-check-fill mr-2"></i> REGISTER ACCESS
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#btn-lookup').click(function() {
        const empno = $('#input_emp_no').val();
        if(!empno) {
            alert('Please enter Employee Number first.');
            return;
        }

        const btn = $(this);
        const originalHtml = btn.html();
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
        $('#lookup-msg').text('Fetching data...').attr('class', 'form-text text-muted');

        $.ajax({
            url: '/admin/users/lookup/' + empno,
            method: 'GET',
            success: function(data) {
                $('#field_name').val(data.name).removeClass('bg-light');
                $('#field_email').val(data.email).removeClass('bg-light');
                $('#field_div').val(data.div).removeClass('bg-light');
                $('#field_dept').val(data.dept).removeClass('bg-light');
                $('#field_post').val(data.position).removeClass('bg-light');
                
                $('#lookup-wrapper').show();
                $('#lookup-msg').html('<i class="bi bi-check-circle-fill"></i> Record found in SY_0100').attr('style', 'background-color: #00e676 !important; color: #ffffff !important; font-weight: 800 !important; border: 1px solid #00c853 !important; display: inline-block; width: 100%; padding: 8px 12px; shadow: 0 2px 4px rgba(0,0,0,0.1);');
                btn.prop('disabled', false).html(originalHtml);
            },
            error: function(xhr) {
                $('#lookup-wrapper').show();
                $('#lookup-msg').html('<i class="bi bi-exclamation-circle-fill"></i> ' + (xhr.responseJSON.error || 'Connection failed.')).css({'background-color': '#dc3545', 'color': '#ffffff', 'border': '1px solid #bd2130'});
                btn.prop('disabled', false).html(originalHtml);
            }
        });
    });

    // Hide message on form reset
    $('#reg-form').on('reset', function() {
        $('#lookup-wrapper').hide();
        $('#lookup-msg').html('');
    });
});
</script>
@endsection

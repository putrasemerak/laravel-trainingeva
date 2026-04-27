@extends('layouts.app')

@section('title', 'System Settings - Admin')

@section('styles')
<style>
    /* Darker, less prominent table lines */
    .table-bordered,
    .table-bordered td,
    .table-bordered th {
        border-color: #4a5568 !important; /* Darker Slate Gray */
    }
    
    [data-theme="light"] .table-bordered,
    [data-theme="light"] .table-bordered td,
    [data-theme="light"] .table-bordered th {
        border-color: #cbd5e0 !important; /* Soft Gray for light mode */
    }

    [data-theme="dark"] .table-bordered,
    [data-theme="dark"] .table-bordered td,
    [data-theme="dark"] .table-bordered th {
        border-color: #2d3748 !important; /* Deep Slate for dark mode */
    }

    .table thead th {
        border-bottom-width: 1px !important;
    }
</style>
@endsection

@section('content')
<div class="row">
    <!-- User Roles Management -->
    <div class="col-md-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">User Access Control (Local)</h6>
                <div class="d-flex gap-2">
                    <div class="input-group input-group-sm mr-2" style="width: 200px;">
                        <input type="text" id="user-search" class="form-control" placeholder="Search user...">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                        </div>
                    </div>
                    <a href="{{ route('admin.users.register') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-person-plus"></i> Register Access
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="user-role-table">
                        <thead>
                            <tr>
                                <th>Emp No</th>
                                <th>Role</th>
                                <th>Updated At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($roles as $role)
                            <tr class="user-row">
                                <td class="emp-no-cell">{{ $role->emp_no }}</td>
                                <td>
                                    @php
                                        $badge = match($role->role) {
                                            'superuser' => 'badge-danger',
                                            'admin' => 'badge-primary',
                                            'evaluator' => 'badge-success',
                                            default => 'badge-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $badge }}">{{ strtoupper($role->role) }}</span>
                                </td>
                                <td>{{ $role->updated_at }}</td>
                                <td>
                                    <form action="{{ route('admin.roles.update') }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="emp_no" value="{{ $role->emp_no }}">
                                        <select name="role" class="form-control form-control-sm d-inline-block w-auto" onchange="this.form.submit()">
                                            <option value="user" {{ $role->role == 'user' ? 'selected' : '' }}>User</option>
                                            <option value="evaluator" {{ $role->role == 'evaluator' ? 'selected' : '' }}>Evaluator</option>
                                            <option value="admin" {{ $role->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                            <option value="superuser" {{ $role->role == 'superuser' ? 'selected' : '' }}>SuperUser</option>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center">No role mappings found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Global System Settings -->
    <div class="col-md-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Global System Settings</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.settings.update') }}" method="POST" class="mb-4">
                    @csrf
                    <div class="form-group">
                        <label>Application Mode</label>
                        <input type="hidden" name="key" value="app_mode">
                        <select name="value" class="form-control" onchange="this.form.submit()">
                            <option value="production">Production</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="development">Development</option>
                        </select>
                    </div>
                </form>

                <div class="alert alert-info small">
                    <i class="bi bi-info-circle"></i> These settings apply to all users globally.
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Page Access Permissions -->
    <div class="col-md-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Page Access Permissions</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Module / Page</th>
                                @foreach($allRoles as $roleName)
                                <th class="text-center">{{ strtoupper($roleName) }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($modules as $module)
                            <tr>
                                <td class="font-weight-bold">{{ ucwords(str_replace('_', ' ', $module)) }}</td>
                                @foreach($allRoles as $roleName)
                                <td class="text-center">
                                    @php
                                        $isAllowed = isset($permissions[$roleName]) && $permissions[$roleName]->where('module', $module)->first() && $permissions[$roleName]->where('module', $module)->first()->is_allowed;
                                    @endphp
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input permission-toggle" 
                                               id="perm_{{ $roleName }}_{{ $module }}"
                                               data-role="{{ $roleName }}"
                                               data-module="{{ $module }}"
                                               {{ $isAllowed || $roleName === 'superuser' ? 'checked' : '' }}
                                               {{ $roleName === 'superuser' ? 'disabled' : '' }}>
                                        <label class="custom-control-label" for="perm_{{ $roleName }}_{{ $module }}"></label>
                                    </div>
                                </td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Role Modal -->
<div class="modal fade" id="addRoleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.roles.update') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">User Management & Role Assignment</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Employee Number (EmpNo)</label>
                        <div class="input-group">
                            <input type="text" name="emp_no" id="modal_emp_no" class="form-control" required placeholder="e.g. C2535">
                            <div class="input-group-append">
                                <button class="btn btn-outline-primary" type="button" id="btn-lookup-user">
                                    <i class="bi bi-search"></i> Verify
                                </button>
                            </div>
                        </div>
                        <small id="lookup-status" class="form-text"></small>
                    </div>

                    <div id="local-fields" style="display: none;" class="p-3 bg-light border rounded mb-3">
                        <h6 class="small font-weight-bold text-primary mb-2">Registration Details (Local)</h6>
                        <div class="form-group">
                            <label class="small">Full Name</label>
                            <input type="text" name="name" id="modal_name" class="form-control form-control-sm">
                        </div>
                        <div class="form-group mb-0">
                            <label class="small">Email Address</label>
                            <input type="email" name="email" id="modal_email" class="form-control form-control-sm">
                        </div>
                        <input type="hidden" name="register_local" id="modal_register_local" value="0">
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Assigned Role</label>
                        <select name="role" class="form-control" required>
                            <option value="user">User (Standard)</option>
                            <option value="evaluator">Evaluator (Supervisor)</option>
                            <option value="admin">Admin (HR)</option>
                            <option value="superuser">SuperUser (IT/Full Access)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="btn-save-role">Save Assignment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Permission toggle logic
    $('.permission-toggle').on('change', function() {
        const role = $(this).data('role');
        const module = $(this).data('module');
        const is_allowed = $(this).is(':checked') ? 1 : 0;

        $.ajax({
            url: "{{ route('admin.permissions.update') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                role: role,
                module: module,
                is_allowed: is_allowed
            },
            success: function(response) {
                console.log('Permission updated');
            },
            error: function(xhr) {
                alert('Error updating permission');
                $(this).prop('checked', !$(this).is(':checked'));
            }
        });
    });

    // User Search Logic
    $('#user-search').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $("#user-role-table tbody tr").filter(function() {
            $(this).toggle($(this).find('.emp-no-cell').text().toLowerCase().indexOf(value) > -1)
        });
    });

    // Employee Lookup Logic
    $('#btn-lookup-user').click(function() {
        const empno = $('#modal_emp_no').val();
        if(!empno) return;

        $('#lookup-status').text('Checking...').attr('class', 'form-text text-muted');
        
        $.ajax({
            url: '/api/employees/' + empno,
            method: 'GET',
            success: function(data) {
                $('#lookup-status').html('<i class="bi bi-check-circle-fill"></i> Found in System: <strong>' + (data.name || data.empname) + '</strong>').attr('class', 'form-text text-success');
                $('#local-fields').hide();
                $('#modal_register_local').val('0');
                $('#modal_name, #modal_email').prop('required', false);
            },
            error: function() {
                $('#lookup-status').html('<i class="bi bi-info-circle-fill"></i> Not found in Legacy. Register as local user?').attr('class', 'form-text text-warning');
                $('#local-fields').slideDown();
                $('#modal_register_local').val('1');
                $('#modal_name, #modal_email').prop('required', true);
            }
        });
    });
});
</script>
@endpush

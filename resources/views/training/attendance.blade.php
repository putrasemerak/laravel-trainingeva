@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm main-card">
                <div class="card-header theme-card-header text-white">
                    <h4 class="mb-0">Training Attendance Entry</h4>
                </div>
                <div class="card-body">
                    <!-- Training Selection Section -->
                    <div class="section-container mb-4 p-3 border rounded theme-section">
                        <h5 class="border-bottom pb-2 section-heading">1. Training Information</h5>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="training_search" class="form-label theme-label">Search Training Title (Dynamic)</label>
                                <div class="position-relative">
                                    <input type="text" id="training_search" class="form-control theme-input" placeholder="Type training title or TRS code...">
                                    <div id="train_spinner" class="spinner-border spinner-border-sm position-absolute text-primary" style="right: 10px; top: 10px; display: none;"></div>
                                    <div id="training_results" class="list-group position-absolute w-100 z-index-1000 shadow-sm theme-dropdown" style="display:none; max-height: 200px; overflow-y: auto;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label theme-label">Training Title</label>
                                <input type="text" id="display_tname" class="form-control theme-input-readonly" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label theme-label">Training Code (TRS)</label>
                                <input type="text" id="display_trs" class="form-control theme-input-readonly" readonly>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label theme-label">Start Date</label>
                                <input type="text" id="display_tstart" class="form-control theme-input-readonly" readonly>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label theme-label">End Date</label>
                                <input type="text" id="display_tend" class="form-control theme-input-readonly" readonly>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label theme-label">Time</label>
                                <input type="text" id="display_ttime" class="form-control theme-input-readonly" readonly>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label theme-label">Venue</label>
                                <input type="text" id="display_tvenue" class="form-control theme-input-readonly" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label theme-label">Trainer Name</label>
                                <input type="text" id="display_trainer" class="form-control theme-input-readonly" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label theme-label">Trainer Type</label>
                                <input type="text" id="display_trainer_type" class="form-control theme-input-readonly" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Participant Selection Section -->
                    <div class="section-container mb-4 p-3 border rounded theme-section">
                        <h5 class="border-bottom pb-2 section-heading">2. Participant Selection</h5>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="employee_search" class="form-label theme-label">Search Employee (ID or Name)</label>
                                <div class="position-relative">
                                    <input type="text" id="employee_search" class="form-control theme-input" placeholder="Type employee number or name...">
                                    <div id="emp_spinner" class="spinner-border spinner-border-sm position-absolute text-primary" style="right: 10px; top: 10px; display: none;"></div>
                                    <div id="employee_results" class="list-group position-absolute w-100 z-index-1000 shadow-sm theme-dropdown" style="display:none; max-height: 200px; overflow-y: auto;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="participant_details">
                            <div class="col-md-3 mb-3">
                                <label class="form-label theme-label">Emp No</label>
                                <input type="text" id="part_empno" class="form-control theme-input-readonly" readonly>
                            </div>
                            <div class="col-md-9 mb-3">
                                <label class="form-label theme-label">Emp Name</label>
                                <input type="text" id="part_name" class="form-control theme-input-readonly" readonly>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label theme-label">Department</label>
                                <input type="text" id="part_dept" class="form-control theme-input-readonly" readonly>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label theme-label">Section</label>
                                <input type="text" id="part_sec" class="form-control theme-input-readonly" readonly>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label theme-label">Supervisor</label>
                                <input type="text" id="part_super" class="form-control theme-input-readonly" readonly>
                                <input type="hidden" id="part_super_no">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label theme-label">Supervisor Email</label>
                                <input type="text" id="part_super_email" class="form-control theme-input-readonly" readonly>
                            </div>
                            <div class="col-md-12 text-end">
                                <button type="button" id="btn_add_participant" class="btn btn-success">
                                    <i class="fas fa-plus"></i> Add Participant to List
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Attendance List Grid -->
                    <div class="section-container mb-4 p-3 border rounded theme-section-white">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="mb-0 section-heading">3. Participant List</h5>
                            <span class="badge bg-info text-dark p-2">Total Participants: <span id="total_count">0</span></span>
                        </div>
                        <div class="table-responsive" style="max-height: 400px;">
                            <table class="table table-striped table-hover border theme-table" id="attendance_table">
                                <thead class="theme-thead sticky-top">
                                    <tr>
                                        <th>Emp No</th>
                                        <th>Name</th>
                                        <th>Department</th>
                                        <th>Supervisor</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Dynamic rows will appear here -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="button" id="btn_save_attendance" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> Save All Attendance & Notify Superiors
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for success message -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="card bg-success text-white w-100">
      <div class="card-body text-center py-4">
        <i class="fas fa-check-circle fa-3x mb-3"></i>
        <h5 id="successMessage">Participant Added!</h5>
      </div>
    </div>
  </div>
</div>

<style>
    .z-index-1000 { z-index: 1000; }
    .section-container { transition: all 0.3s ease; }
    .section-container:hover { border-color: #0d6efd !important; }

    /* Theme-aware styles */
    .main-card {
        background-color: var(--bg-card) !important;
        border-color: var(--border-color) !important;
    }
    .theme-card-header {
        background-color: #007bff !important; /* Keep primary blue for consistency */
    }
    .theme-section {
        background-color: var(--card-header-bg) !important;
        border-color: var(--border-color) !important;
    }
    .theme-section-white {
        background-color: var(--bg-card) !important;
        border-color: var(--border-color) !important;
    }
    .theme-label {
        color: var(--text-body) !important;
        font-weight: 600 !important;
    }
    .section-heading {
        color: var(--text-body) !important;
        border-bottom-color: var(--border-color) !important;
    }
    .theme-input {
        background-color: var(--input-bg) !important;
        color: var(--input-text) !important;
        border-color: var(--input-border) !important;
    }
    .theme-input::placeholder {
        color: var(--text-muted) !important;
        opacity: 0.7;
    }
    .theme-input-readonly {
        background-color: var(--bg-body) !important;
        color: var(--text-body) !important;
        border-color: var(--border-color) !important;
        opacity: 0.8;
    }
    .theme-dropdown {
        background-color: var(--dropdown-bg) !important;
        border-color: var(--border-color) !important;
        color: var(--dropdown-text) !important;
    }
    .theme-dropdown .list-group-item {
        background-color: var(--dropdown-bg) !important;
        color: var(--dropdown-text) !important;
        border-color: var(--border-color) !important;
    }
    .theme-dropdown .list-group-item:hover {
        background-color: #ffcc00 !important;
        color: #000 !important;
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
    
    /* Ensure table text is visible */
    .table td {
        color: var(--text-body) !important;
    }
    
    [data-theme="dark"] .badge-info {
        background-color: #17a2b8 !important;
        color: #fff !important;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let participants = [];
    const trainingSearch = document.getElementById('training_search');
    const trainingResults = document.getElementById('training_results');
    const trainSpinner = document.getElementById('train_spinner');
    
    const employeeSearch = document.getElementById('employee_search');
    const employeeResults = document.getElementById('employee_results');
    const empSpinner = document.getElementById('emp_spinner');
    
    const btnAddParticipant = document.getElementById('btn_add_participant');
    const btnSaveAttendance = document.getElementById('btn_save_attendance');
    const attendanceTableBody = document.querySelector('#attendance_table tbody');
    const totalCountSpan = document.getElementById('total_count');
    const successModal = new bootstrap.Modal(document.getElementById('successModal'));

    // Debounce Helper Function
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Dynamic Training Search (Remote)
    const performTrainingSearch = debounce(function(term) {
        if (term.length < 2) {
            trainingResults.style.display = 'none';
            trainSpinner.style.display = 'none';
            return;
        }

        trainSpinner.style.display = 'block';
        fetch(`/training/search?term=${encodeURIComponent(term)}`)
            .then(res => res.json())
            .then(data => {
                trainingResults.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(item => {
                        const div = document.createElement('a');
                        div.href = '#';
                        div.className = 'list-group-item list-group-item-action';
                        div.innerHTML = `<strong>${item.TRS}</strong> - ${item.TName}`;
                        div.onclick = (e) => {
                            e.preventDefault();
                            selectTraining(item);
                        };
                        trainingResults.appendChild(div);
                    });
                    trainingResults.style.display = 'block';
                } else {
                    trainingResults.style.display = 'none';
                }
            })
            .finally(() => {
                trainSpinner.style.display = 'none';
            });
    }, 300);

    trainingSearch.addEventListener('input', (e) => performTrainingSearch(e.target.value));

    function selectTraining(item) {
        document.getElementById('display_trs').value = item.TRS;
        document.getElementById('display_tname').value = item.TName;
        document.getElementById('display_tstart').value = item.TSDate || '';
        document.getElementById('display_tend').value = item.TEDate || '';
        document.getElementById('display_ttime').value = item.TSTime || '';
        document.getElementById('display_tvenue').value = item.Venue || '';
        document.getElementById('display_trainer').value = item.Trainer || '';
        document.getElementById('display_trainer_type').value = item.TrainerType || '';
        
        trainingSearch.value = item.TName;
        trainingResults.style.display = 'none';
    }

    // Dynamic Employee Search (Local Table - High Performance)
    const performEmployeeSearch = debounce(function(term) {
        if (term.length < 2) {
            employeeResults.style.display = 'none';
            empSpinner.style.display = 'none';
            return;
        }

        empSpinner.style.display = 'block';
        fetch(`/employee/search?term=${encodeURIComponent(term)}`)
            .then(res => res.json())
            .then(data => {
                employeeResults.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(item => {
                        const div = document.createElement('a');
                        div.href = '#';
                        div.className = 'list-group-item list-group-item-action';
                        div.innerHTML = `<strong>${item.empno}</strong> - ${item.empname}`;
                        div.onclick = (e) => {
                            e.preventDefault();
                            selectEmployee(item.empno);
                        };
                        employeeResults.appendChild(div);
                    });
                    employeeResults.style.display = 'block';
                } else {
                    employeeResults.style.display = 'none';
                }
            })
            .finally(() => {
                empSpinner.style.display = 'none';
            });
    }, 300);

    employeeSearch.addEventListener('input', (e) => performEmployeeSearch(e.target.value));

    function selectEmployee(empno) {
        // Detailed data still comes from Remote to ensure latest supervisor info
        empSpinner.style.display = 'block';
        fetch(`/employee/details/${empno}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('part_empno').value = data.empno;
                document.getElementById('part_name').value = data.empname;
                document.getElementById('part_dept').value = data.deptcode;
                document.getElementById('part_sec').value = data.seccode;
                document.getElementById('part_super').value = data.supervisor_name;
                document.getElementById('part_super_no').value = data.supervisor_no;
                document.getElementById('part_super_email').value = data.supervisor_email;

                employeeSearch.value = data.empname;
                employeeResults.style.display = 'none';
            })
            .finally(() => {
                empSpinner.style.display = 'none';
            });
    }

    // Add Participant to List
    btnAddParticipant.addEventListener('click', function() {
        const empno = document.getElementById('part_empno').value;
        if (!empno) {
            alert('Please select an employee first.');
            return;
        }

        if (participants.find(p => p.emp_no === empno)) {
            alert('Employee already added to the list.');
            return;
        }

        const participant = {
            emp_no: empno,
            emp_name: document.getElementById('part_name').value,
            dept_code: document.getElementById('part_dept').value,
            sec_code: document.getElementById('part_sec').value,
            supervisor_no: document.getElementById('part_super_no').value,
            supervisor_name: document.getElementById('part_super').value,
            supervisor_email: document.getElementById('part_super_email').value
        };

        participants.push(participant);
        renderParticipants();
        
        // Show success message and clear fields
        document.getElementById('successMessage').innerText = 'Participant Added!';
        successModal.show();
        setTimeout(() => successModal.hide(), 1000);

        // Clear participant fields
        employeeSearch.value = '';
        document.getElementById('part_empno').value = '';
        document.getElementById('part_name').value = '';
        document.getElementById('part_dept').value = '';
        document.getElementById('part_sec').value = '';
        document.getElementById('part_super').value = '';
        document.getElementById('part_super_no').value = '';
        document.getElementById('part_super_email').value = '';
    });

    function renderParticipants() {
        attendanceTableBody.innerHTML = '';
        participants.forEach((p, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${p.emp_no}</td>
                <td>${p.emp_name}</td>
                <td>${p.dept_code}</td>
                <td>${p.supervisor_name}</td>
                <td class="text-center">
                    <button class="btn btn-sm btn-danger btn-remove" data-index="${index}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
            attendanceTableBody.appendChild(row);
        });

        totalCountSpan.innerText = participants.length;

        // Add event listeners for remove buttons
        document.querySelectorAll('.btn-remove').forEach(btn => {
            btn.onclick = function() {
                const index = this.getAttribute('data-index');
                participants.splice(index, 1);
                renderParticipants();
            };
        });
    }

    // Save All Attendance
    btnSaveAttendance.addEventListener('click', function() {
        const trs = document.getElementById('display_trs').value;
        if (!trs) {
            alert('Please select a training first.');
            return;
        }

        if (participants.length === 0) {
            alert('Please add at least one participant.');
            return;
        }

        if (!confirm('Are you sure you want to save attendance and notify supervisors?')) {
            return;
        }

        const data = {
            training_code: trs,
            training_title: document.getElementById('display_tname').value,
            start_date: document.getElementById('display_tstart').value,
            end_date: document.getElementById('display_tend').value,
            time: document.getElementById('display_ttime').value,
            venue: document.getElementById('display_tvenue').value,
            trainer_name: document.getElementById('display_trainer').value,
            trainer_type: document.getElementById('display_trainer_type').value,
            participants: participants,
            _token: '{{ csrf_token() }}'
        };

        btnSaveAttendance.disabled = true;
        btnSaveAttendance.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Saving...';

        fetch('/training/attendance/store', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                document.getElementById('successMessage').innerText = 'All Attendance Saved & Notified!';
                successModal.show();
                setTimeout(() => {
                    successModal.hide();
                    window.location.reload();
                }, 2000);
            } else {
                alert('Error: ' + result.message);
                btnSaveAttendance.disabled = false;
                btnSaveAttendance.innerHTML = '<i class="fas fa-save"></i> Save All Attendance & Notify Superiors';
            }
        })
        .catch(err => {
            console.error(err);
            alert('An error occurred during save.');
            btnSaveAttendance.disabled = false;
            btnSaveAttendance.innerHTML = '<i class="fas fa-save"></i> Save All Attendance & Notify Superiors';
        });
    });

    // Close results when clicking outside
    document.addEventListener('click', function(e) {
        if (!trainingSearch.contains(e.target) && !trainingResults.contains(e.target)) {
            trainingResults.style.display = 'none';
        }
        if (!employeeSearch.contains(e.target) && !employeeResults.contains(e.target)) {
            employeeResults.style.display = 'none';
        }
    });
});
</script>
@endsection

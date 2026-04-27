@extends('layouts.app')

@section('title', 'Full Evaluation Form')

@section('styles')
<style>
    .rating-row { border-bottom: 1px solid var(--border-color); padding: 15px 0; }
    .rating-row:last-child { border-bottom: none; }
    .range-value { font-weight: 800; font-size: 1.2rem; min-width: 40px; text-align: center; color: #007bff; }
    .custom-range { height: 1.5rem; padding: 0; background: transparent; cursor: pointer; }
    
    .section-header { 
        background: var(--card-header-bg); 
        color: var(--text-body);
        padding: 8px 15px; 
        margin: 25px 0 15px 0; 
        font-weight: 800; 
        border-left: 5px solid #007bff;
        text-transform: uppercase;
        font-size: 0.85rem;
    }
    .main-card { background-color: var(--bg-card) !important; border-color: var(--border-color) !important; }
    .theme-label { color: var(--text-body) !important; font-weight: 600; }
    .theme-input { background-color: var(--input-bg) !important; color: var(--input-text) !important; border-color: var(--input-border) !important; }
</style>
@endsection

@section('content')
<div class="row justify-content-center pb-5">
    <div class="col-md-11">
        <div class="card shadow main-card">
            <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-file-earmark-ruled mr-2"></i> Training Effectiveness Evaluation (Full Form)</h5>
                <span class="badge badge-light px-3">Master Entry Mode</span>
            </div>
            
            <div class="card-body px-4">
                <form action="{{ route('evaluations.store') }}" method="POST">
                    @csrf

                    <!-- 1. PERSONNEL INFORMATION -->
                    <div class="section-header mt-0">1. Personnel Information</div>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="theme-label small">Employee Number</label>
                            <div class="input-group input-group-sm">
                                <input type="text" id="empno_search" name="empno" class="form-control font-weight-bold theme-input" placeholder="e.g. C2535" required>
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="button" id="btn-retrieve">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="theme-label small">Participant Name</label>
                            <input type="text" id="fullname" name="fullname" class="form-control form-control-sm theme-input" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="theme-label small">Reference Number</label>
                            <input type="text" name="refnum" class="form-control form-control-sm theme-input font-weight-bold" placeholder="Auto-generated if blank">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="theme-label small">Division</label>
                            <input type="text" id="div" name="div" class="form-control form-control-sm theme-input" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="theme-label small">Department</label>
                            <input type="text" id="dept" name="dept" class="form-control form-control-sm theme-input" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="theme-label small">Section / Unit</label>
                            <input type="text" id="sec" name="sec" class="form-control form-control-sm theme-input">
                        </div>
                    </div>

                    <!-- 2. TRAINING INFORMATION -->
                    <div class="section-header">2. Training Information</div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="theme-label small">Quick-Fill from Training History</label>
                            <select id="training_selector" class="form-control form-control-sm theme-input border-primary">
                                <option value="">-- No Employee Selected --</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="theme-label small">Training Topic</label>
                            <input id="topic" name="topic" type="text" class="form-control form-control-sm theme-input font-weight-bold" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="theme-label small">Category</label>
                            <select name="radiocom2" id="tcategory" class="form-control form-control-sm theme-input">
                                <option value="Internal">Internal Training</option>
                                <option value="External">External Training</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="theme-label small">Start Date</label>
                            <input id="entryin" name="entryin" type="date" class="form-control form-control-sm theme-input" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="theme-label small">End Date</label>
                            <input id="entryout" name="entryout" type="date" class="form-control form-control-sm theme-input" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="theme-label small">Duration</label>
                            <input id="tduration" name="tduration" type="text" class="form-control form-control-sm theme-input" placeholder="e.g. 2 Days">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="theme-label small">Training Result</label>
                            <select name="tresult" id="tresult" class="form-control form-control-sm theme-input">
                                <option value="Passed">Passed</option>
                                <option value="Failed">Failed</option>
                                <option value="N/A">N/A</option>
                            </select>
                        </div>
                    </div>

                    <!-- 3. EVALUATION SETUP -->
                    <div class="section-header">3. Evaluation Setup & Methodology</div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="theme-label small">Evaluation Methodology</label>
                            <select name="radiocom" class="form-control form-control-sm theme-input" required>
                                <option value="On the job observation">On the job observation</option>
                                <option value="Written or Practical Test">Written or Practical Test</option>
                                <option value="Dummy Project">Dummy Project</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="theme-label small">Status</label>
                            <select name="status" id="status" class="form-control form-control-sm theme-input" required>
                                <option value="To Evaluate">To Evaluate (Pending Superior)</option>
                                <option value="Evaluated">Evaluated (Completed)</option>
                                <option value="To Notify">To Notify</option>
                                <option value="Overdue">Overdue</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="theme-label small">Due Date (6 Months Standard)</label>
                            <input id="duedate" name="duedate" type="date" class="form-control form-control-sm theme-input" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="theme-label small text-primary">Evaluator ID</label>
                            <input id="eemp" name="eemp" type="text" class="form-control form-control-sm theme-input text-primary font-weight-bold" required>
                        </div>
                        <div class="col-md-5 mb-3">
                            <label class="theme-label small text-primary">Evaluator Name</label>
                            <input id="ename" name="ename" type="text" class="form-control form-control-sm theme-input text-primary font-weight-bold" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="theme-label small text-primary">Evaluator Email</label>
                            <input id="eemail" name="eemail" type="email" class="form-control form-control-sm theme-input text-primary font-weight-bold" required>
                        </div>
                    </div>

                    <!-- 4. EFFECTIVENESS RATING -->
                    <div class="section-header text-primary">4. Effectiveness Rating (0-10 Sliders)</div>
                    @php
                        $ratings = [
                            ['name' => 'range', 'label' => 'A. Improvement in Knowledge After the Training'],
                            ['name' => 'range2', 'label' => 'B. Improvement in Skill After the Training'],
                            ['name' => 'range3', 'label' => 'C. Improvement in Delivery & Speed'],
                            ['name' => 'range4', 'label' => 'D. Improvement in Initiative and Cooperation'],
                            ['name' => 'range5', 'label' => 'E. Improvement in Reduction Error & Rework'],
                            ['name' => 'range6', 'label' => 'F. Improvement to Work Quality Output'],
                        ];
                    @endphp
@foreach($ratings as $rating)
<div class="form-group row align-items-center rating-row">
    <label class="col-sm-6 col-form-label small theme-label">{{ $rating['label'] }}</label>
    <div class="col-sm-6">
        <div class="px-2">
            <div class="d-flex align-items-center">
                <input type="range" class="custom-range rating-range flex-grow-1" min="0" max="10" step="1" name="{{ $rating['name'] }}" id="{{ $rating['name'] }}" value="0" list="tickmarks_visible">
                <div class="range-value ml-3" id="{{ $rating['name'] }}_val">0</div>
            </div>
            <div class="d-flex justify-content-between mt-1 text-muted font-weight-bold" style="font-size: 10px; padding-right: 52px; padding-left: 2px;">
                @for($i=0; $i<=10; $i++)
                    <span>{{ $i }}</span>
                @endfor
            </div>
        </div>
    </div>
</div>
@endforeach

<datalist id="tickmarks_visible">
    @for($i=0; $i<=10; $i++) <option value="{{ $i }}"></option> @endfor
</datalist>
                    <div class="form-group mb-3">
                        <label class="theme-label small">Comments by Evaluator / Superior</label>
                        <textarea class="form-control theme-input" name="evaluator" rows="3" placeholder="Enter superior comments here..."></textarea>
                    </div>

                    <div class="row align-items-center p-3 border rounded mb-4 theme-section shadow-sm">
                        <div class="col-md-3 text-center border-right">
                            <h6 class="small text-uppercase mb-1 theme-label">Average Rating</h6>
                            <div class="h2 font-weight-bold mb-0 text-primary" id="totaleffective">0.00</div>
                            <input type="hidden" name="totaleffective" id="totaleffective_input" value="0">
                        </div>
                        <div class="col-md-9 pl-4">
                            <div class="alert mb-0 py-2 border font-weight-bold" id="ratingAlert">
                                <i class="bi bi-info-circle mr-2"></i> Set ratings above to determine effectiveness.
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="theme-label small">Remarks by Training & Development (HR Only)</label>
                        <textarea class="form-control theme-input" name="remarkhr" rows="2" placeholder="Internal HR notes..."></textarea>
                    </div>

                    <div class="text-center mt-5">
                        <hr class="border-top-color">
                        <button type="submit" class="btn btn-success btn-lg px-5 shadow font-weight-bold">
                            <i class="bi bi-save2-fill mr-2"></i> SUBMIT ENTIRE RECORD
                        </button>
                        <p class="mt-3"><a href="{{ route('evaluations') }}" class="text-muted small">Cancel and Return to Dashboard</a></p>
                    </div>

                    <input type="hidden" name="dtissued" value="{{ date('Y-m-d') }}">
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
let globalHistory = [];

$(document).ready(function() {
    $('#btn-retrieve').click(function() {
        let empno = $('#empno_search').val();
        if (!empno) return;
        $(this).html('<span class="spinner-border spinner-border-sm"></span>');

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

                let selector = $('#training_selector');
                selector.empty().append('<option value="">-- Select a Training to Autofill --</option>');
                
                globalHistory = data.training_history || [];
                globalHistory.forEach((item, index) => {
                    let d = item.TDate ? new Date(item.TDate).toLocaleDateString() : 'N/A';
                    selector.append(`<option value="${index}">${item.Title} (${d})</option>`);
                });
                $('#btn-retrieve').html('<i class="bi bi-search"></i>');
            },
            error: function() {
                alert('Employee not found');
                $('#btn-retrieve').html('<i class="bi bi-search"></i>');
            }
        });
    });

    $('#training_selector').change(function() {
        let idx = $(this).val();
        if (idx === "") return;
        let r = globalHistory[idx];
        $('#topic').val(r.Title);
        $('#tcategory').val(r.Category || 'Internal');
        $('#tduration').val(r.Period + ' ' + (r.NOD > 0 ? 'Days' : 'Hrs'));
        
        if (r.TSDate) {
            let td = new Date(r.TSDate);
            $('#entryin').val(td.toISOString().split('T')[0]);
            $('#entryout').val(td.toISOString().split('T')[0]);
            td.setMonth(td.getMonth() + 6);
            $('#duedate').val(td.toISOString().split('T')[0]);
        }
    });

    $('.rating-range').on('input', function() {
        let sum = 0; let count = 0;
        $('.rating-range').each(function() {
            let v = parseInt($(this).val());
            sum += v; count++;
            $(`#${$(this).attr('id')}_val`).text(v);
        });
        let avg = (sum / count).toFixed(2);
        $('#totaleffective').text(avg);
        $('#totaleffective_input').val(avg);

        let alert = $('#ratingAlert');
        if (sum === 0) {
            alert.attr('class', 'alert mb-0 py-2 border font-weight-bold').html('<i class="bi bi-info-circle mr-2"></i> Set ratings above.');
        } else if (avg >= 5) {
            alert.attr('class', 'alert mb-0 py-2 border alert-success text-success font-weight-bold').html('<i class="bi bi-patch-check-fill mr-2"></i> Highly Effective!! No Re-Training Required');
        } else {
            alert.attr('class', 'alert mb-0 py-2 border alert-danger text-danger font-weight-bold').html('<i class="bi bi-exclamation-triangle-fill mr-2"></i> Not Effective!! Re-Training Required');
        }
    });

    $('#entryout').change(function() {
        let d = new Date($(this).val());
        if (!isNaN(d)) {
            d.setMonth(d.getMonth() + 6);
            $('#duedate').val(d.toISOString().split('T')[0]);
        }
    });
});
</script>
@endsection

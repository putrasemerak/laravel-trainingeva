@extends('layouts.app')

@section('title', 'Evaluate Training')

@section('styles')
<style>
    .rating-row { border-bottom: 1px solid var(--border-color); padding: 10px 0; }
    .rating-row:last-child { border-bottom: none; }
    .range-value { font-weight: 800; font-size: 1.1rem; min-width: 30px; text-align: center; color: #4e73df; }
    .custom-range { height: 1.4rem; padding: 0; background: transparent; }
</style>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-9">
        <div class="card shadow-sm mb-4 border-bottom-success">
            <div class="card-header bg-white py-3">
                <h6 class="card-title-compact text-success">
                    <i class="bi bi-pencil-square mr-2"></i> Training Effectiveness Evaluation Form
                </h6>
            </div>
            
            <div class="card-body px-4 py-3">
                @if($evaluation->status === 'Evaluated')
                <div class="alert alert-success border d-flex align-items-center mb-4">
                    <i class="bi bi-lock-fill h4 mb-0 mr-3"></i>
                    <div>
                        <strong>RECORD LOCKED:</strong> This evaluation was completed on <strong>{{ $evaluation->dtevaluate }}</strong>. 
                        Data is now finalized and synced with AINData.
                    </div>
                </div>
                @else
                <div class="alert alert-light border small text-muted mb-4">
                    <i class="bi bi-info-circle"></i> To be filled by Immediate Superiors / Section Head / Department Head within 6 months.
                </div>
                @endif

                <div class="form-compact">
                    <div class="section-title mt-0 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Record Reference</div>
                    
                    <div class="form-group row align-items-center">
                        <label class="col-sm-3 col-form-label">Reference Number</label>
                        <div class="col-sm-4">
                            <input value="{{ $evaluation->refnum }}" class="form-control form-control-sm font-weight-bold bg-light" readonly>
                        </div>
                    </div>

                    <div class="section-title text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Personnel & Training Info</div>

                    <div class="form-group row align-items-center">
                        <label class="col-sm-3 col-form-label">Participant Name</label>
                        <div class="col-sm-9">
                            <input value="{{ $evaluation->fullname }} ({{ $evaluation->empno }})" class="form-control form-control-sm bg-light" readonly>
                        </div>
                    </div>

                    <div class="form-group row align-items-center">
                        <label class="col-sm-3 col-form-label">Division / Dept</label>
                        <div class="col-sm-9">
                            <div class="row">
                                <div class="col-sm-6"><input value="{{ $evaluation->div }}" class="form-control form-control-sm bg-light" readonly></div>
                                <div class="col-sm-6"><input value="{{ $evaluation->dept }}" class="form-control form-control-sm bg-light" readonly></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row align-items-center">
                        <label class="col-sm-3 col-form-label">Training Topic</label>
                        <div class="col-sm-9">
                            <input value="{{ $evaluation->topic }}" class="form-control form-control-sm bg-light font-weight-bold" readonly>
                        </div>
                    </div>

                    <div class="form-group row align-items-center">
                        <label class="col-sm-3 col-form-label">Dates / Duration</label>
                        <div class="col-sm-9">
                            <div class="row">
                                <div class="col-sm-4"><input value="{{ $evaluation->entryin }} to {{ $evaluation->entryout }}" class="form-control form-control-sm bg-light" readonly title="Training Dates"></div>
                                <div class="col-sm-4"><input value="{{ $evaluation->tduration }}" class="form-control form-control-sm bg-light" readonly title="Duration"></div>
                                <div class="col-sm-4"><input value="{{ $evaluation->tresult }}" class="form-control form-control-sm bg-light" readonly title="Result"></div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('evaluations.update', $evaluation->teid) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="section-title text-uppercase text-primary" style="font-size: 0.75rem; letter-spacing: 0.5px;">Effectiveness Rating (0-10 Scale)</div>

                        @php
                            $ratings = [
                                ['name' => 'range', 'label' => 'A. Improvement in Knowledge After the Training'],
                                ['name' => 'range2', 'label' => 'B. Improvement in Skill After the Training'],
                                ['name' => 'range3', 'label' => 'C. Improvement in Delivery & Speed'],
                                ['name' => 'range4', 'label' => 'D. Improvement in Initiative and Cooperation'],
                                ['name' => 'range5', 'label' => 'E. Improvement in Reduction Error & Rework'],
                                ['name' => 'range6', 'label' => 'F. Improvement to Work Quality Output'],
                            ];
                            $isLocked = $evaluation->status === 'Evaluated';
                        @endphp

                        @foreach($ratings as $rating)
                        <div class="form-group row align-items-center rating-row">
                            <label class="col-sm-5 col-form-label small font-weight-bold">{{ $rating['label'] }}</label>
                            <div class="col-sm-7">
                                <div class="d-flex align-items-center px-2">
                                    <input type="range" class="custom-range rating-range flex-grow-1" min="0" max="10" step="1" name="{{ $rating['name'] }}" id="{{ $rating['name'] }}" 
                                        value="{{ $evaluation->{$rating['name']} ?? 0 }}" 
                                        {{ $isLocked ? 'disabled' : '' }}
                                        list="tickmarks" style="cursor: {{ $isLocked ? 'default' : 'pointer' }};">
                                    <div class="range-value ml-3" id="{{ $rating['name'] }}_val">{{ $evaluation->{$rating['name']} ?? 0 }}</div>
                                </div>
                                <div class="d-flex justify-content-between mt-1 text-muted font-weight-bold" style="font-size: 9px !important; padding-left: 14px; padding-right: 52px;">
                                    <span>0</span><span>1</span><span>2</span><span>3</span><span>4</span><span>5</span><span>6</span><span>7</span><span>8</span><span>9</span><span>10</span>
                                </div>
                            </div>
                        </div>
                        @endforeach

                        <datalist id="tickmarks">
                            <option value="0"></option>
                            <option value="1"></option>
                            <option value="2"></option>
                            <option value="3"></option>
                            <option value="4"></option>
                            <option value="5"></option>
                            <option value="6"></option>
                            <option value="7"></option>
                            <option value="8"></option>
                            <option value="9"></option>
                            <option value="10"></option>
                        </datalist>

                        <div class="section-title text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Evaluator Conclusion</div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Comments</label>
                            <div class="col-sm-9">
                                <textarea class="form-control form-control-sm" name="evaluator" rows="3" {{ $isLocked ? 'disabled' : '' }} placeholder="Enter your observation or feedback here...">{{ $evaluation->evaluator }}</textarea>
                            </div>
                        </div>

                        <div class="mt-4 p-3 border rounded bg-light">
                            <div class="row align-items-center">
                                <div class="col-md-3 text-center border-right">
                                    <h6 class="small text-uppercase mb-1">Overall Rating</h6>
                                    <div class="h3 font-weight-bold mb-0" id="totaleffective">{{ $evaluation->totaleffective ?? '0.00' }}</div>
                                    <input type="hidden" name="totaleffective" id="totaleffective_input" value="{{ $evaluation->totaleffective ?? 0 }}">
                                </div>
                                <div class="col-md-9 pl-4">
                                    <div class="alert mb-0 py-2 px-3 border" id="ratingAlert">
                                        <i class="bi bi-info-circle mr-1"></i> Please complete the ratings above.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 text-center">
                            <hr>
                            @if(!$isLocked)
                                <button type="submit" class="btn btn-success px-5 py-2 font-weight-bold shadow-sm">
                                    <i class="bi bi-check-circle mr-1"></i> SUBMIT FINAL EVALUATION
                                </button>
                            @else
                                <a href="{{ route('evaluations.print', $evaluation->teid) }}" target="_blank" class="btn btn-info px-5 py-2 font-weight-bold shadow-sm">
                                    <i class="bi bi-printer mr-1"></i> PRINT EVALUATION
                                </a>
                            @endif
                            <a href="{{ route('evaluations') }}" class="btn btn-link btn-sm text-muted">Back to List</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    function calculateAverage() {
        var sum = 0;
        var count = 0;
        $('.rating-range').each(function() {
            var val = parseInt($(this).val());
            sum += val;
            count++;
            $('#' + $(this).attr('id') + '_val').text(val);
        });

        var average = sum / count;
        var displayAvg = average.toFixed(2);
        $('#totaleffective').text(displayAvg);
        $('#totaleffective_input').val(displayAvg);

        var alertElement = $('#ratingAlert');
        if (average >= 5) {
            alertElement.removeClass('alert-light alert-danger').addClass('alert-success');
            alertElement.html('<i class="bi bi-patch-check-fill mr-2"></i> <strong>Highly Effective!!</strong> No Re-Training Required');
        } else if (sum > 0) {
            alertElement.removeClass('alert-light alert-success').addClass('alert-danger');
            alertElement.html('<i class="bi bi-exclamation-triangle-fill mr-2"></i> <strong>Not Effective!!</strong> Re-Training Required');
        }
    }

    $('.rating-range').on('input', function() {
        calculateAverage();
    });

    calculateAverage();
});
</script>
@endsection

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>AMSB Training Effectiveness Evaluation : {{ $evaluation->refnum }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        body { font-size: 14px; }
        .table-bordered th, .table-bordered td { border: 1px solid #000 !important; }
        @media print {
            .page-break { page-break-before: always; margin-top: 3rem !important; }
            .no-print { display: none; }
            .footer { position: fixed; bottom: 0; width: 100%; font-size: 12px; text-align: left; }
        }
        .footer { font-size: 12px; }
    </style>
</head>
<body onload="window.print()">
    <div class="container py-4">
        <div class="no-print mb-3 text-right">
            <button class="btn btn-primary" onclick="window.print()">Print</button>
            <button class="btn btn-secondary" onclick="window.close()">Close</button>
        </div>

        <table class="table table-bordered">
            <tr>
                <th scope="col" width="40%" class="text-center">
                    <img src="/ain_logo.png" width="180px">
                </th>
                <th scope="col" class="text-center">
                    Ain Medicare Sdn Bhd <br> Training Effectiveness Evaluation
                </th>
            </tr>
        </table>

        <h4 class="mt-4">Individual Record</h4>
        <table class="table table-bordered">
            <tr><th width="30%">Reference No :</th><td>{{ $evaluation->refnum }}</td></tr>
            <tr><th>Name :</th><td>{{ $evaluation->fullname }}</td></tr>
            <tr><th>Employee No :</th><td>{{ $evaluation->empno }}</td></tr>
            <tr><th>Division :</th><td>{{ $evaluation->div }}</td></tr>
            <tr><th>Department :</th><td>{{ $evaluation->dept }}</td></tr>
            <tr><th>Section :</th><td>{{ $evaluation->sec }}</td></tr>
            <tr><th>Sub-Section :</th><td>{{ $evaluation->subsec }}</td></tr>
            <tr><th>Unit :</th><td>{{ $evaluation->unit }}</td></tr>
        </table>

        <h4 class="mt-4">Training Information</h4>
        <table class="table table-bordered">
            <tr><th width="30%">Category of Training :</th><td>{{ $evaluation->tcategory }}</td></tr>
            <tr><th>Training Topic :</th><td>{{ $evaluation->topic }}</td></tr>
            <tr><th>Start Date :</th><td>{{ \Carbon\Carbon::parse($evaluation->entryin)->format('d/M/Y') }}</td></tr>
            <tr><th>End Date :</th><td>{{ \Carbon\Carbon::parse($evaluation->entryout)->format('d/M/Y') }}</td></tr>
            <tr><th>Training Duration :</th><td>{{ $evaluation->tduration }}</td></tr>
        </table>

        <h4 class="mt-4">Evaluation Information</h4>
        <table class="table table-bordered">
            <tr><th width="30%">Status of Evaluation :</th><td>{{ $evaluation->status }}</td></tr>
            <tr><th>Due Date :</th><td>{{ \Carbon\Carbon::parse($evaluation->duedate)->format('d/M/Y') }}</td></tr>
            <tr><th>Evaluator :</th><td>{{ $evaluation->ename }} ({{ $evaluation->eemp }})</td></tr>
            <tr><th>Evaluator Email :</th><td>{{ $evaluation->eemail }}</td></tr>
        </table>

        <div class="page-break"></div>

        <h4 class="mt-4">Training Effectiveness Evaluation</h4>
        <table class="table table-bordered">
            <tr><th width="50%">Improvement in Knowledge After the Training :</th><td>{{ $evaluation->range }} / 10</td></tr>
            <tr><th>Improvement in Skill After the Training :</th><td>{{ $evaluation->range2 }} / 10</td></tr>
            <tr><th>Improvement in Delivery & Speed :</th><td>{{ $evaluation->range3 }} / 10</td></tr>
            <tr><th>Improvement in Initiative and Cooperation :</th><td>{{ $evaluation->range4 }} / 10</td></tr>
            <tr><th>Improvement in Reduction Error & Rework :</th><td>{{ $evaluation->range5 }} / 10</td></tr>
            <tr><th>Improvement to Work Quality Output :</th><td>{{ $evaluation->range6 }} / 10</td></tr>
            <tr><th>Total Rating :</th><td>{{ $evaluation->totaleffective }} / 10</td></tr>
            <tr><th>Evaluator Comment :</th><td>{{ $evaluation->evaluator }}</td></tr>
        </table>

        <h4 class="mt-4">Summary of Evaluation</h4>
        <table class="table table-bordered">
            <tr><th width="50%">Total Rating :</th><td>{{ $evaluation->totaleffective }}</td></tr>
            @php
                $isEffective = $evaluation->totaleffective >= 5;
            @endphp
            <tr><th>Effectiveness Rating :</th><td>{{ $isEffective ? 'Effective' : 'Not Effective' }}</td></tr>
            <tr><th>Re-Training Required :</th><td>{{ $isEffective ? 'No Re-Training Required' : 'Re-Training Required' }}</td></tr>
            <tr><th>Evaluation Date :</th><td>{{ $evaluation->dtevaluate ? \Carbon\Carbon::parse($evaluation->dtevaluate)->format('d/M/Y') : 'N/A' }}</td></tr>
        </table>

        <p class="mt-4">This report is computer generated and does not require signatures.</p>

        <div class="footer mt-5">
            <div><b>Form No. :</b> F-Y-020-R5</div>
            <div><b>Effective Date :</b> 28 May 2025</div>
        </div>
    </div>
</body>
</html>

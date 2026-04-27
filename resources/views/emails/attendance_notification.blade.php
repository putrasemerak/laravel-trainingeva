<!DOCTYPE html>
<html>
<head>
    <title>Training Attendance Notification</title>
</head>
<body>
    <p>Dear {{ $evaluation->ename }},</p>

    <p>This is to inform you that your subordinate, <strong>{{ $evaluation->fullname }} ({{ $evaluation->empno }})</strong>, has attended the following training:</p>

    <ul>
        <li><strong>Training Title:</strong> {{ $evaluation->topic }}</li>
        <li><strong>Reference No:</strong> {{ $evaluation->refnum }}</li>
        <li><strong>Date:</strong> {{ $evaluation->entryin }} to {{ $evaluation->entryout }}</li>
        <li><strong>Details:</strong> {{ $evaluation->remarkhr }}</li>
    </ul>

    <p>Please be informed that an auto-email will be sent to you in the next 3 months to request your evaluation of this training via the following link:</p>
    
    <p><a href="{{ url('/evaluations/' . $evaluation->teid . '/evaluate') }}">Supervisor Evaluation Link</a></p>

    <p>Thank you.</p>
    <p>Best regards,<br>Training Administration System</p>
</body>
</html>

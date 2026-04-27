<!DOCTYPE html>
<html>
<head>
    <title>Training Effectiveness Evaluation Reminder</title>
</head>
<body style="font-family: sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 5px;">
        <h2 style="color: #2d3748; border-bottom: 2px solid #3b82f6; padding-bottom: 10px;">Training Evaluation Request</h2>
        
        <p>Dear <strong>{{ $evaluation->ename }}</strong>,</p>

        <p>This is an automated reminder that the 3-month post-training period for your subordinate has concluded. We now require your professional evaluation of the training effectiveness.</p>

        <div style="background-color: #f7fafc; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            <p style="margin: 5px 0;"><strong>Employee Name:</strong> {{ $evaluation->fullname }} ({{ $evaluation->empno }})</p>
            <p style="margin: 5px 0;"><strong>Training Topic:</strong> {{ $evaluation->topic }}</p>
            <p style="margin: 5px 0;"><strong>Training Date:</strong> {{ $evaluation->entryin }}</p>
            <p style="margin: 5px 0;"><strong>Reference No:</strong> {{ $evaluation->refnum }}</p>
        </div>

        <p>Please click the button below to complete the effectiveness evaluation form:</p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ url('/evaluations/' . $evaluation->teid . '/evaluate') }}" 
               style="background-color: #3b82f6; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">
               Complete Evaluation Now
            </a>
        </div>

        <p style="color: #718096; font-size: 0.9em;">
            If the button above does not work, copy and paste this link into your browser:<br>
            <span style="color: #3182ce;">{{ url('/evaluations/' . $evaluation->teid . '/evaluate') }}</span>
        </p>

        <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">

        <p>Thank you for your cooperation in ensuring the quality of our training programs.</p>
        
        <p>Best regards,<br>
        <strong>Human Resources Department</strong><br>
        Training Administration System</p>
    </div>
</body>
</html>

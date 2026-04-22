<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; border: 1px solid #e1e1e1; border-radius: 8px; overflow: hidden; }
        .header { background-color: #025ca7; color: white; padding: 20px; text-align: center; }
        .content { padding: 25px; }
        .footer { background-color: #f8f9fa; padding: 15px; text-align: center; font-size: 12px; color: #777; }
        .btn { display: inline-block; padding: 12px 25px; background-color: #1cc88a; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 20px; }
        .info-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .info-table th { text-align: left; padding: 8px; border-bottom: 1px solid #eee; width: 40%; }
        .info-table td { padding: 8px; border-bottom: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 style="margin:0;">Training Evaluation System</h2>
        </div>
        <div class="content">
            <p>Dear <strong>{{ $evaluation->ename }}</strong>,</p>
            <p>You have been assigned as the evaluator for a training completed by your team member. Please review and complete the effectiveness evaluation within the specified due date.</p>
            
            <table class="info-table">
                <tr><th>Participant:</th><td>{{ $evaluation->fullname }} ({{ $evaluation->empno }})</td></tr>
                <tr><th>Training Topic:</th><td>{{ $evaluation->topic }}</td></tr>
                <tr><th>Dates:</th><td>{{ $evaluation->entryin }} to {{ $evaluation->entryout }}</td></tr>
                <tr><th>Due Date:</th><td><strong>{{ $evaluation->duedate }}</strong></td></tr>
            </table>

            <div style="text-align: center;">
                <a href="{{ url('/evaluations/' . $evaluation->teid . '/evaluate') }}" class="btn">Proceed to Evaluation</a>
            </div>

            <p style="margin-top: 30px;">Thank you,<br><strong>Training & Development Department</strong><br>Ain Medicare Sdn. Bhd.</p>
        </div>
        <div class="footer">
            This is an automated notification. Please do not reply to this email.
        </div>
    </div>
</body>
</html>

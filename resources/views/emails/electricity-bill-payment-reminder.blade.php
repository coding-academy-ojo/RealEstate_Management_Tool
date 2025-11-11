<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Electricity Bill Payment Reminder</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background-color: #dc3545;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }

        .content {
            background-color: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 5px 5px;
        }

        .alert {
            background-color: #f8d7da;
            border-left: 4px solid #dc3545;
            padding: 15px;
            margin: 20px 0;
        }

        .stats-container {
            display: flex;
            gap: 20px;
            margin: 20px 0;
        }

        .stat-box {
            flex: 1;
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
        }

        .stat-number {
            font-size: 36px;
            font-weight: bold;
            color: #dc3545;
        }

        .stat-label {
            color: #6c757d;
            font-size: 14px;
            margin-top: 5px;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            color: #6c757d;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>⚡ Electricity Bill Payment Reminder</h1>
    </div>

    <div class="content">
        <div class="alert">
            <strong>⚠️ Unpaid Bills - Payment Due Soon</strong>
        </div>

        <p>Hello,</p>

        <p>This is a reminder that you have unpaid electricity bills that are due by the end of the month.</p>

        <div class="stats-container">
            <div class="stat-box">
                <div class="stat-number">{{ $unpaidCount }}</div>
                <div class="stat-label">Unpaid Bills</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">${{ number_format($totalAmount, 2) }}</div>
                <div class="stat-label">Total Amount Due</div>
            </div>
        </div>

        <p><strong>Action Required:</strong></p>
        <ul>
            <li>Log into the Real Estate Management System</li>
            <li>Navigate to Electricity Services</li>
            <li>Review unpaid bills</li>
            <li>Process payments</li>
            <li>Mark bills as paid in the system</li>
        </ul>

        <p style="background-color: #fff3cd; padding: 15px; border-radius: 5px; margin-top: 20px;">
            <strong>💡 Tip:</strong> You can filter unpaid bills on the Electricity Services page to quickly identify
            which services require payment.
        </p>

        <p style="margin-top: 30px;">
            <strong>⏰ Deadline:</strong> Please ensure all payments are processed before the end of the month to avoid
            late fees or service interruptions.
        </p>
    </div>

    <div class="footer">
        <p>This is an automated reminder from the Real Estate Management System.</p>
        <p>Please do not reply to this email.</p>
    </div>
</body>

</html>

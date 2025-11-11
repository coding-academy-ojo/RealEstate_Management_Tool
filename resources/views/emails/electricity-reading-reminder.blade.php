<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Electricity Reading Reminder</title>
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
            background-color: #ffc107;
            color: #000;
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
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
        }

        .stats {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
        }

        .stat-number {
            font-size: 48px;
            font-weight: bold;
            color: #ffc107;
        }

        .stat-label {
            color: #6c757d;
            font-size: 16px;
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
        <h1>⚡ Electricity Reading Reminder</h1>
    </div>

    <div class="content">
        <div class="alert">
            <strong>📅 Monthly Reading Day - 5th of the Month</strong>
        </div>

        <p>Hello,</p>

        <p>This is your monthly reminder to add electricity meter readings for all active services.</p>

        <div class="stats">
            <div class="stat-number">{{ $servicesCount }}</div>
            <div class="stat-label">Active Electricity Services</div>
        </div>

        <p><strong>What to do:</strong></p>
        <ul>
            <li>Log into the Real Estate Management System</li>
            <li>Navigate to Electricity Services</li>
            <li>Add current meter readings for all active services</li>
            <li>For solar services, include imported, produced, and saved energy readings</li>
            <li>Include bill amounts and payment status</li>
            <li>Upload meter/bill photos if available</li>
        </ul>

        <p style="margin-top: 30px; text-align: center;">
            <strong>💡 Tip:</strong> Use the Bulk Add Readings feature to add multiple readings quickly!
        </p>

        <p style="background-color: #e7f3ff; padding: 15px; border-radius: 5px; margin-top: 20px;">
            <strong>🌞 Solar Services:</strong> Don't forget to record produced energy (المُنتَجة) and saved energy
            (المخزّنة) for services with solar power systems.
        </p>

        <p style="background-color: #fff3cd; padding: 15px; border-radius: 5px; margin-top: 20px;">
            <strong>⏰ Remember:</strong> Bill payment reminders will be sent 5 days before the end of the month for any
            unpaid bills.
        </p>
    </div>

    <div class="footer">
        <p>This is an automated reminder from the Real Estate Management System.</p>
        <p>Please do not reply to this email.</p>
    </div>
</body>

</html>

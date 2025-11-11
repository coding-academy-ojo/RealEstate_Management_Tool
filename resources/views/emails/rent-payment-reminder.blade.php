<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rent Payment Reminder</title>
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
            background-color: #0d6efd;
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
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
        }

        .details {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }

        .details-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #dee2e6;
        }

        .details-row:last-child {
            border-bottom: none;
        }

        .label {
            font-weight: bold;
            color: #6c757d;
        }

        .value {
            color: #212529;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            color: #6c757d;
            font-size: 14px;
        }

        .amount {
            font-size: 24px;
            font-weight: bold;
            color: #0d6efd;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>🏢 Rent Payment Reminder</h1>
    </div>

    <div class="content">
        <div class="alert">
            <strong>⚠️ Payment Due in {{ $daysUntilDue }} Days</strong>
        </div>

        <p>Hello,</p>

        <p>This is a reminder that the rent payment for the following property is due soon:</p>

        <div class="details">
            <div class="details-row">
                <span class="label">Building Name:</span>
                <span class="value">{{ $building->name }}</span>
            </div>
            <div class="details-row">
                <span class="label">Building Code:</span>
                <span class="value">{{ $building->code }}</span>
            </div>
            <div class="details-row">
                <span class="label">Site:</span>
                <span class="value">{{ $building->site->name ?? 'N/A' }}</span>
            </div>
            <div class="details-row">
                <span class="label">Payment Frequency:</span>
                <span class="value">{{ ucfirst(str_replace('-', ' ', $building->contract_payment_frequency)) }}</span>
            </div>
            <div class="details-row">
                <span class="label">Next Payment Date:</span>
                <span class="value"><strong>{{ $nextPaymentDate }}</strong></span>
            </div>
            <div class="details-row">
                <span class="label">Contract Value:</span>
                <span class="value amount">${{ number_format($building->contract_value, 2) }}</span>
            </div>
        </div>

        @if ($building->special_conditions)
            <div style="background-color: #e7f3ff; padding: 15px; border-radius: 5px; margin-top: 20px;">
                <strong>Special Conditions:</strong>
                <p style="margin: 10px 0 0 0;">{{ $building->special_conditions }}</p>
            </div>
        @endif

        <p style="margin-top: 30px;">
            <strong>Action Required:</strong> Please ensure the payment is processed before the due date.
        </p>

        <p>
            <strong>Contract Period:</strong><br>
            {{ \Carbon\Carbon::parse($building->contract_start_date)->format('F j, Y') }}
            to
            {{ \Carbon\Carbon::parse($building->contract_end_date)->format('F j, Y') }}
        </p>
    </div>

    <div class="footer">
        <p>This is an automated reminder from the Real Estate Management System.</p>
        <p>Please do not reply to this email.</p>
    </div>
</body>

</html>

# Email Notification System

This system sends automated email reminders to admins and engineers for rent payments, water readings/bills, and electricity readings/bills.

## Features

### 1. Rent Payment Reminders

-   **When**: 14 days before payment is due
-   **Frequency**: Based on contract payment frequency (monthly, quarterly, semi-annual, annual)
-   **Recipients**: Admins, Super Admins, Engineers with building privileges
-   **Command**: `php artisan reminders:rent-payments`
-   **Schedule**: Daily at 9:00 AM (Asia/Amman timezone)

### 2. Water Reminders

-   **Reading Reminder**: 5th day of each month
-   **Bill Payment Reminder**: 5 days before month end
-   **Recipients**: Admins, Super Admins, Engineers with water privileges
-   **Command**: `php artisan reminders:water`
-   **Schedule**: Daily at 8:00 AM (Asia/Amman timezone)

### 3. Electricity Reminders

-   **Reading Reminder**: 5th day of each month
-   **Bill Payment Reminder**: 5 days before month end
-   **Recipients**: Admins, Super Admins, Engineers with electricity privileges
-   **Command**: `php artisan reminders:electricity`
-   **Schedule**: Daily at 8:30 AM (Asia/Amman timezone)

## Setup Instructions

### 1. Configure Email Settings

Edit `.env` file with your email configuration:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="Real Estate Management"
```

For Gmail, you need to:

1. Enable 2-Factor Authentication
2. Generate an App Password
3. Use the App Password in `MAIL_PASSWORD`

### 2. Set Up Task Scheduler

**For Windows (Laragon/XAMPP):**

Create a scheduled task that runs every minute:

1. Open Task Scheduler
2. Create Basic Task
3. Name: "Laravel Scheduler"
4. Trigger: Daily, repeat every 1 minute for 24 hours
5. Action: Start a program
6. Program: `C:\laragon\bin\php\php-8.x.x\php.exe`
7. Arguments: `artisan schedule:run`
8. Start in: `C:\laragon\www\RealState`

**Command:**

```batch
"C:\laragon\bin\php\php-8.3.0\php.exe" "C:\laragon\www\RealState\artisan" schedule:run
```

**For Linux/Production:**
Add to crontab:

```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

### 3. Test Commands Manually

Test each command individually:

```bash
# Test rent payment reminders
php artisan reminders:rent-payments

# Test water reminders
php artisan reminders:water

# Test electricity reminders
php artisan reminders:electricity
```

## How It Works

### Recipient Selection

**Admins & Super Admins**: Receive all notifications

**Engineers**: Receive notifications based on privileges:

-   Rent: Engineers with `sites_lands_buildings` privilege
-   Water: Engineers with `water` or `sites_lands_buildings` privilege
-   Electricity: Engineers with `electricity` or `sites_lands_buildings` privilege

### Rent Payment Logic

1. Calculates next payment date based on:

    - Contract start date
    - Contract payment frequency
    - Current date

2. Sends reminder when exactly 14 days before payment due

3. Example for monthly rent:
    - Contract starts: Jan 1, 2025
    - First payment due: Feb 1, 2025
    - Reminder sent: Jan 18, 2025
    - Next payment due: Mar 1, 2025
    - Reminder sent: Feb 15, 2025

### Water & Electricity Logic

**Reading Reminders** (5th of month):

-   Counts active services
-   Sends reminder to add readings
-   Mentions bulk upload feature

**Bill Payment Reminders** (5 days before month end):

-   Counts unpaid bills where `is_paid = false`
-   Calculates total amount due
-   Sends urgency reminder

## Email Templates

All email templates are in `resources/views/emails/`:

-   `rent-payment-reminder.blade.php`
-   `water-reading-reminder.blade.php`
-   `water-bill-payment-reminder.blade.php`
-   `electricity-reading-reminder.blade.php`
-   `electricity-bill-payment-reminder.blade.php`

Templates include:

-   Responsive HTML design
-   Clear call-to-action
-   Relevant statistics
-   Professional styling

## Troubleshooting

### Emails not sending?

1. Check `.env` email configuration
2. Verify SMTP credentials
3. Check `storage/logs/laravel.log` for errors
4. Test with: `php artisan tinker` then `Mail::raw('Test', function($m) { $m->to('test@example.com')->subject('Test'); });`

### Scheduler not running?

1. Verify Windows Task Scheduler is active
2. Check task history in Task Scheduler
3. Manually run: `php artisan schedule:run`
4. Check `storage/logs/laravel.log`

### Wrong timezone?

Update timezone in `routes/console.php`:

```php
->timezone('Asia/Amman')  // Change to your timezone
```

## Customization

### Change Reminder Days

**Rent (14 days before):**
Edit `app/Console/Commands/SendRentPaymentReminders.php` line ~58:

```php
if ($daysUntilDue == 14) {  // Change to desired days
```

**Water/Electricity Reading Day (5th):**
Edit respective command files, line ~36:

```php
if ($dayOfMonth == 5) {  // Change to desired day
```

**Water/Electricity Bill Reminder (5 days before month end):**
Edit respective command files, line ~43:

```php
if ($dayOfMonth == ($daysInMonth - 5)) {  // Change number
```

### Change Schedule Times

Edit `routes/console.php`:

```php
Schedule::command('reminders:rent-payments')
    ->daily()
    ->at('09:00')  // Change time (24-hour format)
```

## Database Requirements

Ensure these fields exist:

-   `buildings`: `property_type`, `contract_payment_frequency`, `contract_start_date`, `contract_end_date`, `contract_value`
-   `water_readings`: `is_paid`, `bill_amount`
-   `electric_readings`: `is_paid`, `bill_amount`
-   `water_services`: `is_active`
-   `electricity_services`: `is_active`
-   `users`: `email`, `role`, `privileges`

## Support

For issues or questions, contact the development team.

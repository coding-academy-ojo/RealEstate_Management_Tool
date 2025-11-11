<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Scheduled Tasks for Email Reminders
Schedule::command('reminders:rent-payments')
    ->daily()
    ->at('09:00')
    ->timezone('Asia/Amman')
    ->description('Send rent payment reminders 14 days before due date');

Schedule::command('reminders:water')
    ->daily()
    ->at('08:00')
    ->timezone('Asia/Amman')
    ->description('Send water reading and bill payment reminders');

Schedule::command('reminders:electricity')
    ->daily()
    ->at('08:30')
    ->timezone('Asia/Amman')
    ->description('Send electricity reading and bill payment reminders');

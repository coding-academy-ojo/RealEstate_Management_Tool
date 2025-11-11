<?php

namespace App\Console\Commands;

use App\Mail\ElectricityBillPaymentReminder;
use App\Mail\ElectricityReadingReminder;
use App\Models\ElectricityService;
use App\Models\ElectricReading;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendElectricityReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:electricity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send electricity reading and bill payment reminders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();
        $dayOfMonth = $today->day;

        // Reading reminder on 5th of each month
        if ($dayOfMonth == 5) {
            $this->sendReadingReminder();
        }

        // Bill payment reminder 5 days before month end
        $daysInMonth = $today->daysInMonth;
        if ($dayOfMonth == ($daysInMonth - 5)) {
            $this->sendBillPaymentReminder();
        }

        return Command::SUCCESS;
    }

    /**
     * Send reading reminder to admins and engineers.
     */
    private function sendReadingReminder(): void
    {
        $servicesCount = ElectricityService::where('is_active', true)->count();

        if ($servicesCount == 0) {
            $this->info('No active electricity services found.');
            return;
        }

        $recipients = $this->getRecipients();

        foreach ($recipients as $user) {
            Mail::to($user->email)->send(new ElectricityReadingReminder($servicesCount));
        }

        $this->info("Electricity reading reminders sent to {$recipients->count()} user(s) for {$servicesCount} active service(s).");
    }

    /**
     * Send bill payment reminder to admins and engineers.
     */
    private function sendBillPaymentReminder(): void
    {
        // Get unpaid bills
        $unpaidReadings = ElectricReading::where('is_paid', false)
            ->whereNotNull('bill_amount')
            ->where('bill_amount', '>', 0)
            ->get();

        $unpaidCount = $unpaidReadings->count();
        $totalAmount = $unpaidReadings->sum('bill_amount');

        if ($unpaidCount == 0) {
            $this->info('No unpaid electricity bills found.');
            return;
        }

        $recipients = $this->getRecipients();

        foreach ($recipients as $user) {
            Mail::to($user->email)->send(new ElectricityBillPaymentReminder($unpaidCount, $totalAmount));
        }

        $this->info("Electricity bill payment reminders sent to {$recipients->count()} user(s) for {$unpaidCount} unpaid bill(s).");
    }

    /**
     * Get admins and engineers who should receive notifications.
     */
    private function getRecipients()
    {
        // Get admins and super admins (they get all notifications)
        $admins = User::whereIn('role', ['admin', 'super_admin'])->get();

        // Get engineers with ONLY electricity privilege (not multiple privileges)
        $engineers = User::where('role', 'engineer')
            ->whereNotNull('privileges')
            ->get()
            ->filter(function ($user) {
                $privileges = $user->privileges ?? [];

                // Only include if user has exactly one privilege and it's 'electricity'
                return count($privileges) === 1 && in_array('electricity', $privileges);
            });

        return $admins->merge($engineers)->unique('id');
    }
}

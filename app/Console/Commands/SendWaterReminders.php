<?php

namespace App\Console\Commands;

use App\Mail\WaterBillPaymentReminder;
use App\Mail\WaterReadingReminder;
use App\Models\User;
use App\Models\WaterReading;
use App\Models\WaterService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendWaterReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:water';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send water reading and bill payment reminders';

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
        $servicesCount = WaterService::where('is_active', true)->count();

        if ($servicesCount == 0) {
            $this->info('No active water services found.');
            return;
        }

        $recipients = $this->getRecipients();

        foreach ($recipients as $user) {
            Mail::to($user->email)->send(new WaterReadingReminder($servicesCount));
        }

        $this->info("Water reading reminders sent to {$recipients->count()} user(s) for {$servicesCount} active service(s).");
    }

    /**
     * Send bill payment reminder to admins and engineers.
     */
    private function sendBillPaymentReminder(): void
    {
        // Get unpaid bills
        $unpaidReadings = WaterReading::where('is_paid', false)
            ->whereNotNull('bill_amount')
            ->where('bill_amount', '>', 0)
            ->get();

        $unpaidCount = $unpaidReadings->count();
        $totalAmount = $unpaidReadings->sum('bill_amount');

        if ($unpaidCount == 0) {
            $this->info('No unpaid water bills found.');
            return;
        }

        $recipients = $this->getRecipients();

        foreach ($recipients as $user) {
            Mail::to($user->email)->send(new WaterBillPaymentReminder($unpaidCount, $totalAmount));
        }

        $this->info("Water bill payment reminders sent to {$recipients->count()} user(s) for {$unpaidCount} unpaid bill(s).");
    }

    /**
     * Get admins and engineers who should receive notifications.
     */
    private function getRecipients()
    {
        // Get admins and super admins (they get all notifications)
        $admins = User::whereIn('role', ['admin', 'super_admin'])->get();

        // Get engineers with ONLY water privilege (not multiple privileges)
        $engineers = User::where('role', 'engineer')
            ->whereNotNull('privileges')
            ->get()
            ->filter(function ($user) {
                $privileges = $user->privileges ?? [];

                // Only include if user has exactly one privilege and it's 'water'
                return count($privileges) === 1 && in_array('water', $privileges);
            });

        return $admins->merge($engineers)->unique('id');
    }
}

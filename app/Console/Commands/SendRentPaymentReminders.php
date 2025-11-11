<?php

namespace App\Console\Commands;

use App\Mail\RentPaymentReminder;
use App\Models\Building;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendRentPaymentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:rent-payments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send rent payment reminders 14 days before payment is due';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();

        // Get all rented buildings with active contracts
        $buildings = Building::where('property_type', 'rented')
            ->whereNotNull('contract_start_date')
            ->whereNotNull('contract_end_date')
            ->whereNotNull('contract_payment_frequency')
            ->where('contract_end_date', '>=', $today)
            ->get();

        $remindersSent = 0;

        foreach ($buildings as $building) {
            $nextPaymentDate = $this->calculateNextPaymentDate($building, $today);

            if (!$nextPaymentDate) {
                continue;
            }

            $daysUntilDue = $today->diffInDays($nextPaymentDate, false);

            // Send reminder 14 days before payment is due
            if ($daysUntilDue == 14) {
                $this->sendReminderToAdminsAndEngineers($building, $daysUntilDue, $nextPaymentDate);
                $remindersSent++;

                $this->info("Sent reminder for: {$building->name} (Due: {$nextPaymentDate->format('Y-m-d')})");
            }
        }

        $this->info("Total reminders sent: {$remindersSent}");

        return Command::SUCCESS;
    }

    /**
     * Calculate the next payment date based on frequency.
     */
    private function calculateNextPaymentDate(Building $building, Carbon $today): ?Carbon
    {
        $startDate = Carbon::parse($building->contract_start_date);
        $endDate = Carbon::parse($building->contract_end_date);
        $frequency = $building->contract_payment_frequency;

        if ($today->lt($startDate)) {
            return $startDate;
        }

        if ($today->gt($endDate)) {
            return null;
        }

        $current = $startDate->copy();

        while ($current->lte($today)) {
            switch ($frequency) {
                case 'monthly':
                    $current->addMonth();
                    break;
                case 'quarterly':
                    $current->addMonths(3);
                    break;
                case 'semi-annual':
                    $current->addMonths(6);
                    break;
                case 'annual':
                    $current->addYear();
                    break;
                default:
                    return null;
            }
        }

        return $current->lte($endDate) ? $current : null;
    }

    /**
     * Send reminder to admins and engineers.
     */
    private function sendReminderToAdminsAndEngineers(Building $building, int $daysUntilDue, Carbon $nextPaymentDate): void
    {
        // Get admins and super admins
        $admins = User::whereIn('role', ['admin', 'super_admin'])->get();

        // Get engineers (users with engineering privileges)
        $engineers = User::where('role', 'engineer')
            ->orWhere(function ($query) {
                $query->where('privileges', 'like', '%sites_lands_buildings%');
            })
            ->get();

        $recipients = $admins->merge($engineers)->unique('id');

        foreach ($recipients as $user) {
            Mail::to($user->email)->send(
                new RentPaymentReminder($building, $daysUntilDue, $nextPaymentDate->format('F j, Y'))
            );
        }
    }
}

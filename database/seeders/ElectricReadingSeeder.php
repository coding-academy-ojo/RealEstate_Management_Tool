<?php

namespace Database\Seeders;

use App\Models\ElectricReading;
use App\Models\ElectricityService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ElectricReadingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = ElectricityService::all();

        foreach ($services as $service) {
            $isSolar = $service->has_solar_power;
            $startDate = Carbon::now()->subMonths(12);
            
            // Initialize tracking variables for progressive readings
            $lastImportedCalculated = rand(5000, 15000); // Starting meter reading
            $lastProducedCalculated = $isSolar ? rand(1000, 5000) : null;
            
            // Generate 12 months of readings
            for ($i = 0; $i < 12; $i++) {
                $readingDate = $startDate->copy()->addMonths($i);
                
                // Skip future dates
                if ($readingDate->isFuture()) {
                    continue;
                }
                
                // Generate imported energy (grid consumption)
                $monthlyImportedConsumption = rand(200, 800); // kWh consumed this month
                $newImportedCalculated = $lastImportedCalculated + $monthlyImportedConsumption;
                $importedCurrent = $newImportedCalculated + rand(5, 50); // Current always higher than calculated
                
                // For solar services, generate production data
                $producedCurrent = null;
                $newProducedCalculated = null;
                $savedEnergy = null;
                $consumptionValue = $monthlyImportedConsumption;
                
                if ($isSolar) {
                    $monthlyProduction = rand(300, 1000); // kWh produced this month
                    $newProducedCalculated = $lastProducedCalculated + $monthlyProduction;
                    $producedCurrent = $newProducedCalculated + rand(5, 40);
                    
                    // Saved energy: production minus consumption
                    $savedEnergy = max(0, $monthlyProduction - $monthlyImportedConsumption);
                    
                    // Net consumption for solar (can be negative if producing more than consuming)
                    $consumptionValue = $monthlyImportedConsumption - $monthlyProduction;
                }
                
                // Calculate bill amount (simplified pricing)
                $billAmount = $this->calculateBillAmount($consumptionValue, $isSolar);
                
                // Determine if paid (80% chance of being paid, higher for older bills)
                $isPaid = $i < 10 ? (rand(1, 100) <= 85) : (rand(1, 100) <= 60);
                
                ElectricReading::create([
                    'electric_service_id' => $service->id,
                    'imported_current' => round($importedCurrent, 2),
                    'imported_calculated' => round($newImportedCalculated, 2),
                    'produced_current' => $producedCurrent ? round($producedCurrent, 2) : null,
                    'produced_calculated' => $newProducedCalculated ? round($newProducedCalculated, 2) : null,
                    'saved_energy' => $savedEnergy ? round($savedEnergy, 2) : null,
                    'consumption_value' => round($consumptionValue, 2),
                    'bill_amount' => round($billAmount, 2),
                    'is_paid' => $isPaid,
                    'reading_date' => $readingDate->format('Y-m-d'),
                    'notes' => $this->generateNotes($isSolar, $isPaid, $consumptionValue),
                ]);
                
                // Update last calculated values for next iteration
                $lastImportedCalculated = $newImportedCalculated;
                if ($isSolar) {
                    $lastProducedCalculated = $newProducedCalculated;
                }
            }
        }
    }
    
    /**
     * Calculate bill amount based on consumption
     */
    private function calculateBillAmount(float $consumption, bool $isSolar): float
    {
        if ($isSolar && $consumption < 0) {
            // Credit for excess production (net metering)
            return abs($consumption) * 0.08; // Lower rate for credits
        }
        
        // Tiered pricing structure (simplified Jordanian tariff)
        $bill = 0;
        $remaining = abs($consumption);
        
        // Tier 1: First 160 kWh at 0.055 JOD/kWh
        if ($remaining > 0) {
            $tier1 = min($remaining, 160);
            $bill += $tier1 * 0.055;
            $remaining -= $tier1;
        }
        
        // Tier 2: 161-300 kWh at 0.09 JOD/kWh
        if ($remaining > 0) {
            $tier2 = min($remaining, 140);
            $bill += $tier2 * 0.09;
            $remaining -= $tier2;
        }
        
        // Tier 3: 301-500 kWh at 0.12 JOD/kWh
        if ($remaining > 0) {
            $tier3 = min($remaining, 200);
            $bill += $tier3 * 0.12;
            $remaining -= $tier3;
        }
        
        // Tier 4: 501+ kWh at 0.15 JOD/kWh
        if ($remaining > 0) {
            $bill += $remaining * 0.15;
        }
        
        // Add fixed service charge
        $bill += 2.5;
        
        return $bill;
    }
    
    /**
     * Generate contextual notes for readings
     */
    private function generateNotes(bool $isSolar, bool $isPaid, float $consumption): ?string
    {
        $notes = [];
        
        if ($isSolar) {
            if ($consumption < 0) {
                $notes[] = 'Net metering credit applied - excess solar production';
            } else if ($consumption < 100) {
                $notes[] = 'Low net consumption due to solar generation';
            }
        }
        
        if (!$isPaid && rand(1, 100) <= 30) {
            $reasons = [
                'Payment pending approval',
                'Awaiting bank transfer',
                'Dispute under review',
                'Scheduled for next payment cycle'
            ];
            $notes[] = $reasons[array_rand($reasons)];
        }
        
        if ($consumption > 700) {
            $notes[] = 'High consumption period - summer cooling';
        }
        
        return count($notes) > 0 ? implode('. ', $notes) : null;
    }
}

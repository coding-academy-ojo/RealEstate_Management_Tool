<?php

namespace App\Http\Controllers;

use App\Exports\Templates\WaterReadingsBulkTemplate;
use App\Models\WaterReading;
use App\Models\WaterService;
use App\Services\WaterReadingManager;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class WaterReadingBulkUploadController extends Controller
{
    /** @var string */
    private const SESSION_PREFIX = 'water_readings_bulk_upload_';

    public function __construct()
    {
        $this->middleware(['auth', 'privilege:water']);
    }

    /**
     * Display the bulk upload interface.
     */
    public function show()
    {
        return view('water-services.bulk-upload');
    }

    /**
     * Download the bulk upload template populated with existing services.
     */
    public function template()
    {
        $fileName = 'water-readings-template-' . now()->format('Ymd') . '.xls';

        return Excel::download(new WaterReadingsBulkTemplate(), $fileName, \Maatwebsite\Excel\Excel::XLS);
    }

    /**
     * Parse the uploaded file and return a preview payload as JSON.
     */
    public function preview(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $uploadedFile = $request->file('file');

        $storageDisk = Storage::disk('local');
        $temporaryDirectory = 'bulk-water-uploads';
        $storageDisk->makeDirectory($temporaryDirectory);

        $extension = strtolower($uploadedFile->getClientOriginalExtension() ?: $uploadedFile->guessExtension() ?: 'xlsx');
        $temporaryFilename = (string) Str::uuid() . '.' . $extension;
        $relativePath = $uploadedFile->storeAs($temporaryDirectory, $temporaryFilename, 'local');
        $absolutePath = $storageDisk->path($relativePath);

        try {
            // Use Xls reader for older Excel format (doesn't require ZipArchive)
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($absolutePath);
        } catch (\Throwable $exception) {
            // Fallback to Xlsx if file is actually .xlsx
            try {
                $reader = new XlsxReader();
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($absolutePath);
            } catch (\Throwable $xlsxException) {
                Log::error('Water bulk upload Excel parsing failed.', [
                    'file' => $uploadedFile->getClientOriginalName(),
                    'xls_error' => $exception->getMessage(),
                    'xlsx_error' => $xlsxException->getMessage(),
                ]);

                $storageDisk->delete($relativePath);

                return response()->json([
                    'success' => false,
                    'message' => 'Unable to read the uploaded file. Please download the template again and ensure you fill it correctly.',
                ], 422);
            }
        }

        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        $storageDisk->delete($relativePath);

        if (empty($sheetData)) {
            return response()->json([
                'success' => false,
                'message' => 'The uploaded sheet is empty.',
            ], 422);
        }

        $headerRow = array_shift($sheetData);
        $columnMap = $this->buildColumnMap($headerRow);

        $requiredColumns = ['registration_number', 'reading_date', 'current_reading', 'bill_amount'];

        foreach ($requiredColumns as $column) {
            if (!isset($columnMap[$column])) {
                return response()->json([
                    'success' => false,
                    'message' => 'The uploaded file is missing the required "' . $this->columnLabel($column) . '" column.',
                ], 422);
            }
        }

        $validRows = [];
        $errorsByType = [
            'missing_registration' => [],
            'inactive_service' => [],
            'missing_required' => [],
            'invalid_date' => [],
            'invalid_numeric' => [],
        ];
        $rowNumber = 2; // Because headers occupy row 1

        foreach ($sheetData as $row) {
            // Skip empty rows quietly
            if ($this->rowIsEmpty($row)) {
                $rowNumber++;
                continue;
            }

            $registration = trim((string) ($row[$columnMap['registration_number']] ?? ''));

            if ($registration === '') {
                $errorsByType['missing_registration'][] = $rowNumber;
                $rowNumber++;
                continue;
            }

            $service = WaterService::with(['building', 'waterCompany', 'latestReading'])
                ->where('registration_number', $registration)
                ->first();

            if (!$service || !$service->is_active) {
                $errorsByType['inactive_service'][] = $rowNumber;
                $rowNumber++;
                continue;
            }

            $readingDateRaw = $row[$columnMap['reading_date']] ?? null;
            $currentReadingRaw = $row[$columnMap['current_reading']] ?? null;
            $billAmountRaw = $row[$columnMap['bill_amount']] ?? null;

            if ($this->isEmptyValue($readingDateRaw) || $this->isEmptyValue($currentReadingRaw) || $this->isEmptyValue($billAmountRaw)) {
                $errorsByType['missing_required'][] = $rowNumber;
                $rowNumber++;
                continue;
            }

            $readingDate = $this->parseDateValue($readingDateRaw);

            if (!$readingDate) {
                $errorsByType['invalid_date'][] = $rowNumber;
                $rowNumber++;
                continue;
            }

            if (!is_numeric($currentReadingRaw)) {
                $currentReadingRaw = is_string($currentReadingRaw) ? str_replace(',', '', $currentReadingRaw) : $currentReadingRaw;
            }

            if (!is_numeric($billAmountRaw)) {
                $billAmountRaw = is_string($billAmountRaw) ? str_replace(',', '', $billAmountRaw) : $billAmountRaw;
            }

            if (!is_numeric($currentReadingRaw) || !is_numeric($billAmountRaw)) {
                $errorsByType['invalid_numeric'][] = $rowNumber;
                $rowNumber++;
                continue;
            }

            $isPaidRaw = isset($columnMap['is_paid']) ? $row[$columnMap['is_paid']] ?? null : null;
            $notesRaw = isset($columnMap['notes']) ? $row[$columnMap['notes']] ?? null : null;

            $previousReading = (float) (optional($service->latestReading)->current_reading ?? 0);

            $entry = [
                'row_number' => $rowNumber,
                'water_service_id' => $service->id,
                'registration_number' => $service->registration_number,
                'iron_number' => $service->iron_number,
                'meter_owner_name' => $service->meter_owner_name,
                'company_name' => optional($service->waterCompany)->name ?? $service->company_name,
                'building_name' => optional($service->building)->name ?? 'Unassigned',
                'reading_date' => $readingDate->format('Y-m-d'),
                'current_reading' => (float) $currentReadingRaw,
                'bill_amount' => (float) $billAmountRaw,
                'is_paid' => $this->parseBoolean($isPaidRaw),
                'notes' => $this->stringify($notesRaw),
                'previous_reading' => $previousReading,
            ];

            $validRows[] = $entry;
            $rowNumber++;
        }

        // Build consolidated error messages
        $errors = [];

        if (!empty($errorsByType['missing_registration'])) {
            $count = count($errorsByType['missing_registration']);
            $errors[] = "{$count} row(s) skipped due to missing registration number.";
        }

        if (!empty($errorsByType['inactive_service'])) {
            $count = count($errorsByType['inactive_service']);
            $errors[] = "{$count} row(s) skipped - no active water service found for the registration number.";
        }

        if (!empty($errorsByType['missing_required'])) {
            $count = count($errorsByType['missing_required']);
            $errors[] = "{$count} row(s) skipped - missing Reading Date, Current Reading, or Bill Amount.";
        }

        if (!empty($errorsByType['invalid_date'])) {
            $count = count($errorsByType['invalid_date']);
            $errors[] = "{$count} row(s) skipped - invalid date format.";
        }

        if (!empty($errorsByType['invalid_numeric'])) {
            $count = count($errorsByType['invalid_numeric']);
            $errors[] = "{$count} row(s) skipped - Current Reading and Bill Amount must be numeric.";
        }

        if (empty($validRows)) {
            return response()->json([
                'success' => false,
                'message' => 'No valid rows were found in the uploaded file.',
                'errors' => $errors,
            ], 422);
        }

        $uploadKey = (string) Str::uuid();
        $sessionKey = self::SESSION_PREFIX . $uploadKey;

        $request->session()->put($sessionKey, [
            'rows' => collect($validRows)->map(function (array $row) {
                return [
                    'water_service_id' => $row['water_service_id'],
                    'registration_number' => $row['registration_number'],
                    'reading_date' => $row['reading_date'],
                    'current_reading' => $row['current_reading'],
                    'bill_amount' => $row['bill_amount'],
                    'is_paid' => $row['is_paid'],
                    'notes' => $row['notes'],
                    'row_number' => $row['row_number'],
                ];
            })->values()->all(),
            'user_id' => $request->user()->id,
            'created_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'upload_key' => $uploadKey,
            'rows' => collect($validRows)->map(function (array $row) {
                return [
                    'row_number' => $row['row_number'],
                    'registration_number' => $row['registration_number'],
                    'iron_number' => $row['iron_number'] ?: 'N/A',
                    'meter_owner_name' => $row['meter_owner_name'],
                    'company_name' => $row['company_name'],
                    'building_name' => $row['building_name'],
                    'previous_reading' => number_format($row['previous_reading'], 2),
                    'reading_date' => $row['reading_date'],
                    'current_reading' => number_format($row['current_reading'], 2),
                    'bill_amount' => number_format($row['bill_amount'], 2),
                    'is_paid' => $row['is_paid'] ? 'Yes' : 'No',
                    'notes' => $row['notes'] ?: 'N/A',
                ];
            })->values()->all(),
            'errors' => $errors,
        ]);
    }

    /**
     * Persist the previously previewed readings into the database.
     */
    public function confirm(Request $request)
    {
        $validated = $request->validate([
            'upload_key' => 'required|string',
        ]);

        $sessionKey = self::SESSION_PREFIX . $validated['upload_key'];
        $payload = $request->session()->get($sessionKey);

        if (!$payload || ($payload['user_id'] ?? null) !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'The uploaded batch is no longer available. Please upload the template again.',
            ], 422);
        }

        $rows = $payload['rows'] ?? [];

        if (empty($rows)) {
            return response()->json([
                'success' => false,
                'message' => 'The uploaded batch does not contain any rows to import.',
            ], 422);
        }

        $serviceIds = collect($rows)->pluck('water_service_id')->unique()->values();
        $services = WaterService::whereIn('id', $serviceIds)->get()->keyBy('id');

        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                WaterReading::create([
                    'water_service_id' => $row['water_service_id'],
                    'reading_date' => $row['reading_date'],
                    'current_reading' => $row['current_reading'],
                    'bill_amount' => $row['bill_amount'],
                    'is_paid' => $row['is_paid'],
                    'notes' => $row['notes'],
                ]);
            }
        });

        foreach ($services as $service) {
            WaterReadingManager::recalculate($service->fresh());
        }

        $request->session()->forget($sessionKey);

        return response()->json([
            'success' => true,
            'message' => count($rows) . ' reading(s) have been added successfully.',
        ]);
    }

    /**
     * Map the header row to internal column keys.
     */
    private function buildColumnMap(array $headerRow): array
    {
        $map = [];

        $expected = [
            'registration #' => 'registration_number',
            'registration number' => 'registration_number',
            'iron #' => 'iron_number',
            'iron number' => 'iron_number',
            'meter owner' => 'meter_owner_name',
            'meter owner name' => 'meter_owner_name',
            'water company' => 'company_name',
            'building' => 'building_name',
            'previous reading' => 'previous_reading',
            'previous reading (m3)' => 'previous_reading',
            'reading date' => 'reading_date',
            'reading date (yyyy-mm-dd)' => 'reading_date',
            'current reading (m3)' => 'current_reading',
            'current reading (m³)' => 'current_reading',
            'bill amount (jod)' => 'bill_amount',
            'paid (yes/no)' => 'is_paid',
            'payment status' => 'is_paid',
            'notes' => 'notes',
        ];

        foreach ($headerRow as $columnLetter => $heading) {
            $normalized = $this->normalizeHeading($heading);

            if (isset($expected[$normalized])) {
                $map[$expected[$normalized]] = $columnLetter;
            }
        }

        return $map;
    }

    private function normalizeHeading($value): string
    {
        $value = is_string($value) ? $value : '';
        $value = strtolower(trim($value));
        $value = preg_replace('/\s+/', ' ', $value);

        return $value ?? '';
    }

    private function rowIsEmpty(array $row): bool
    {
        foreach ($row as $value) {
            if (!$this->isEmptyValue($value)) {
                return false;
            }
        }

        return true;
    }

    private function isEmptyValue($value): bool
    {
        if ($value === null) {
            return true;
        }

        if ($value instanceof \DateTimeInterface) {
            return false;
        }

        if (is_string($value)) {
            return trim($value) === '';
        }

        return false;
    }

    private function parseDateValue($value): ?Carbon
    {
        if ($value instanceof \DateTimeInterface) {
            return Carbon::parse($value->format('Y-m-d'));
        }

        if (is_numeric($value)) {
            try {
                $dateTime = ExcelDate::excelToDateTimeObject((float) $value);
                return Carbon::parse($dateTime->format('Y-m-d'));
            } catch (\Throwable $exception) {
                return null;
            }
        }

        $stringValue = trim((string) $value);

        if ($stringValue === '') {
            return null;
        }

        try {
            return Carbon::parse($stringValue);
        } catch (\Throwable $exception) {
            return null;
        }
    }

    private function parseBoolean($value): bool
    {
        if ($value === null) {
            return false;
        }

        if (is_bool($value)) {
            return $value;
        }

        $stringValue = strtolower(trim((string) $value));

        if ($stringValue === '') {
            return false;
        }

        if (in_array($stringValue, ['yes', 'y', '1', 'true', 'paid'], true)) {
            return true;
        }

        if (in_array($stringValue, ['no', 'n', '0', 'false', 'unpaid', 'pending'], true)) {
            return false;
        }

        return false;
    }

    private function stringify($value): string
    {
        if ($value === null) {
            return '';
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        return trim((string) $value);
    }

    private function columnLabel(string $key): string
    {
        return match ($key) {
            'registration_number' => 'Registration #',
            'reading_date' => 'Reading Date',
            'current_reading' => 'Current Reading (m3)',
            'bill_amount' => 'Bill Amount (JOD)',
            default => ucfirst(str_replace('_', ' ', $key)),
        };
    }
}

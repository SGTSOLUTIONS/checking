<?php

namespace App\Imports;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class MisImport implements ToCollection, WithHeadingRow
{
    protected $tableName;
    protected $corporationId;
    protected $importedCount = 0;
    protected $updatedCount = 0;
    protected $errorCount = 0;

    public function __construct($tableName, $corporationId)
    {
        $this->tableName = $tableName;
        $this->corporationId = $corporationId;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            try {
                // Normalize column names - handle different possible column names
                $wardNo = $this->cleanValue(
                    $row['ward_no'] ?? $row['wardno'] ?? $row['ward'] ?? $row['ward_number'] ?? null
                );

                $assessment = $this->cleanValue(
                    $row['assessment'] ?? $row['assessment_no'] ?? $row['assessment_number'] ?? null
                );

                // Skip if essential data is missing
                if (!$assessment || !$wardNo) {
                    $this->errorCount++;
                    Log::warning("Skipping row {$index} - missing ward_no or assessment");
                    continue;
                }

                // Prepare data for insert/update
                $data = [
                    'corporation_id' => $this->corporationId,
                    'ward_no' => $wardNo,
                    'assessment' => $assessment,
                    'old_assessment' => $this->cleanValue($row['old_assessment'] ?? $row['old_assessment_no'] ?? null),

                    'road_name' => $this->cleanValue($row['road_name'] ?? $row['road_name'] ?? $row['road_name'] ?? null),
                    'owner_name' => $this->cleanValue($row['owner_name'] ?? $row['ownername'] ?? $row['owner'] ?? null),
                    'old_door_no' => $this->cleanValue($row['old_door_no'] ?? $row['old_doorno'] ?? $row['old_door_number'] ?? null),
                    'new_door_no' => $this->cleanValue($row['new_door_no'] ?? $row['new_doorno'] ?? $row['new_door_number'] ?? null),
                    'phone_number' => $this->cleanValue($row['phone_number'] ?? $row['phone'] ?? $row['mobile'] ?? null),
                    'plot_area' => $this->cleanNumeric($row['plot_area'] ?? $row['area'] ?? null),
                    'half_year_tax' => $this->cleanNumeric($row['half_year_tax'] ?? $row['halfyear_tax'] ?? $row['tax'] ?? null),
                    'balance' => $this->cleanNumeric($row['balance'] ?? $row['due_balance'] ?? null),
                    'usage' => $this->cleanEnum($row['usage'] ?? $row['usage_type'] ?? null, 'usage'),
                    'type' => $this->cleanEnum($row['type'] ?? $row['owner_type'] ?? null, 'type'),
                    'zone' => $this->cleanValue($row['zone'] ?? null , 'zone'),
                    'updated_at' => now(),
                ];

                // Check if record exists
                $existing = DB::table($this->tableName)
                    ->where('corporation_id', $this->corporationId)
                    ->where('ward_no', $wardNo)
                    ->where('assessment', $assessment)
                    ->first();

                if ($existing) {
                    // Update existing record
                    DB::table($this->tableName)
                        ->where('id', $existing->id)
                        ->update($data);
                    $this->updatedCount++;
                } else {
                    // Insert new record
                    $data['created_at'] = now();
                    DB::table($this->tableName)->insert($data);
                    $this->importedCount++;
                }

            } catch (\Exception $e) {
                $this->errorCount++;
                Log::error("Row {$index} failed in MIS import for {$this->tableName}: " . $e->getMessage());
                // Continue with next row instead of stopping entire import
                continue;
            }
        }

        Log::info("✅ MIS Import completed for {$this->tableName}: " .
            "Imported: {$this->importedCount}, Updated: {$this->updatedCount}, Errors: {$this->errorCount}");
    }

    private function cleanValue($value)
    {
        if (is_null($value)) {
            return null;
        }

        $value = is_string($value) ? trim($value) : $value;

        // Handle empty strings, 'NULL', 'null', etc.
        if ($value === '' || $value === 'NULL' || $value === 'null') {
            return null;
        }

        return $value;
    }

    private function cleanNumeric($value)
    {
        $value = $this->cleanValue($value);

        if (is_null($value)) {
            return null;
        }

        // Remove any non-numeric characters except decimal point
        if (is_string($value)) {
            $value = preg_replace('/[^\d.-]/', '', $value);
        }

        return is_numeric($value) ? (float) $value : null;
    }

    private function cleanEnum($value, $type)
    {
        $value = $this->cleanValue($value);

        if (is_null($value)) {
            return null;
        }

        $enums = [
            'usage' => ['Residential', 'Commercial', 'Industrial', 'Institutional', 'Vacant'],
            'type' => ['Owner', 'Tenant', 'Mixed', 'Government', 'Others']
        ];

        // Case-insensitive check
        foreach ($enums[$type] as $validValue) {
            if (strtolower($value) === strtolower($validValue)) {
                return $validValue;
            }
        }

        return null;
    }

    // Get import statistics
    public function getImportStats(): array
    {
        return [
            'imported' => $this->importedCount,
            'updated' => $this->updatedCount,
            'errors' => $this->errorCount
        ];
    }
}

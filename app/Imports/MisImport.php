<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MisImport implements ToCollection, WithHeadingRow, WithChunkReading
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

    /**
     * Process rows in chunks
     */
    public function chunkSize(): int
    {
        return 500;
    }

    /**
     * Main import function
     */
    public function collection(Collection $rows)
    {
        try {

            $batchData = [];

            foreach ($rows as $index => $row) {

                try {

                    // =========================
                    // Normalize columns
                    // =========================

                    $wardNo = $this->cleanValue(
                        $row['ward_no']
                            ?? $row['wardno']
                            ?? $row['ward']
                            ?? $row['ward_number']
                            ?? null
                    );

                    $assessment = $this->cleanValue(
                        $row['assessment']
                            ?? $row['assessment_no']
                            ?? $row['assessment_number']
                            ?? null
                    );

                    // Skip invalid rows
                    if (!$wardNo || !$assessment) {

                        $this->errorCount++;

                        Log::warning("Skipping row {$index} - Missing ward_no or assessment");

                        continue;
                    }

                    // =========================
                    // Prepare data
                    // =========================

                    $data = [

                        'corporation_id' => $this->corporationId,

                        'ward_no' => $wardNo,

                        'assessment' => $assessment,

                        'old_assessment' => $this->cleanValue(
                            $row['old_assessment']
                                ?? $row['old_assessment_no']
                                ?? null
                        ),

                        'road_name' => $this->cleanValue(
                            $row['road_name']
                                ?? null
                        ),

                        'owner_name' => $this->cleanValue(
                            $row['owner_name']
                                ?? $row['ownername']
                                ?? $row['owner']
                                ?? null
                        ),

                        'old_door_no' => $this->cleanValue(
                            $row['old_door_no']
                                ?? $row['old_doorno']
                                ?? null
                        ),

                        'new_door_no' => $this->cleanValue(
                            $row['new_door_no']
                                ?? $row['new_doorno']
                                ?? null
                        ),

                        'phone_number' => $this->cleanValue(
                            $row['phone_number']
                                ?? $row['phone']
                                ?? $row['mobile']
                                ?? null
                        ),

                        'plot_area' => $this->cleanNumeric(
                            $row['plot_area']
                                ?? $row['area']
                                ?? null
                        ),

                        'half_year_tax' => $this->cleanNumeric(
                            $row['half_year_tax']
                                ?? $row['halfyear_tax']
                                ?? $row['tax']
                                ?? null
                        ),

                        'balance' => $this->cleanNumeric(
                            $row['balance']
                                ?? $row['due_balance']
                                ?? null
                        ),

                        'usage' => $this->cleanEnum(
                            $row['usage']
                                ?? $row['usage_type']
                                ?? null,
                            'usage'
                        ),

                        'type' => $this->cleanEnum(
                            $row['type']
                                ?? $row['owner_type']
                                ?? null,
                            'type'
                        ),

                        'zone' => $this->cleanValue(
                            $row['zone']
                                ?? null
                        ),

                        'created_at' => now(),

                        'updated_at' => now(),
                    ];

                    $batchData[] = $data;

                    $this->importedCount++;

                } catch (\Exception $e) {

                    $this->errorCount++;

                    Log::error(
                        "Row {$index} failed in MIS import: " . $e->getMessage()
                    );

                    continue;
                }
            }

            // =========================
            // Bulk UPSERT
            // =========================

            if (!empty($batchData)) {

                DB::table($this->tableName)->upsert(

                    $batchData,

                    // Unique columns
                    ['corporation_id', 'ward_no', 'assessment'],

                    // Columns to update
                    [
                        'old_assessment',
                        'road_name',
                        'owner_name',
                        'old_door_no',
                        'new_door_no',
                        'phone_number',
                        'plot_area',
                        'half_year_tax',
                        'balance',
                        'usage',
                        'type',
                        'zone',
                        'updated_at'
                    ]
                );
            }

            Log::info(
                "✅ MIS Import Completed for {$this->tableName} | " .
                "Imported: {$this->importedCount} | " .
                "Errors: {$this->errorCount}"
            );

        } catch (\Exception $e) {

            Log::error(
                "❌ MIS Import Failed for {$this->tableName}: " .
                $e->getMessage()
            );
        }
    }

    /**
     * Clean normal values
     */
    private function cleanValue($value)
    {
        if (is_null($value)) {
            return null;
        }

        $value = is_string($value)
            ? trim($value)
            : $value;

        if (
            $value === ''
            || $value === 'NULL'
            || $value === 'null'
        ) {
            return null;
        }

        return $value;
    }

    /**
     * Clean numeric values
     */
    private function cleanNumeric($value)
    {
        $value = $this->cleanValue($value);

        if (is_null($value)) {
            return null;
        }

        if (is_string($value)) {
            $value = preg_replace('/[^\d.-]/', '', $value);
        }

        return is_numeric($value)
            ? (float) $value
            : null;
    }

    /**
     * Clean enum values
     */
    private function cleanEnum($value, $type)
    {
        $value = $this->cleanValue($value);

        if (is_null($value)) {
            return null;
        }

        $enums = [

            'usage' => [
                'Residential',
                'Commercial',
                'Industrial',
                'Institutional',
                'Vacant'
            ],

            'type' => [
                'Owner',
                'Tenant',
                'Mixed',
                'Government',
                'Others'
            ]
        ];

        foreach ($enums[$type] as $validValue) {

            if (strtolower($value) === strtolower($validValue)) {

                return $validValue;
            }
        }

        return null;
    }

    /**
     * Import statistics
     */
    public function getImportStats(): array
    {
        return [

            'imported' => $this->importedCount,

            'updated' => $this->updatedCount,

            'errors' => $this->errorCount
        ];
    }
}

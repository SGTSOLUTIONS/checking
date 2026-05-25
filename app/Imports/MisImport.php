<?php

namespace App\Imports;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;

use Maatwebsite\Excel\Row;

use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class MisImport implements
    OnEachRow,
    WithHeadingRow,
    WithChunkReading,
    WithBatchInserts,
    WithCustomCsvSettings,
    ShouldQueue
{
    protected $tableName;
    protected $corporationId;

    protected $batchData = [];

    protected $importedCount = 0;
    protected $errorCount = 0;

    public function __construct($tableName, $corporationId)
    {
        $this->tableName = $tableName;
        $this->corporationId = $corporationId;
    }

    /**
     * Process each row
     */
    public function onRow(Row $row)
    {
        try {

            $row = $row->toArray();

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

                Log::warning(
                    "Skipping row {$row->getIndex()} - Missing ward_no or assessment"
                );

                return;
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

            $this->batchData[] = $data;

            $this->importedCount++;

            // =========================
            // Bulk insert every 100 rows
            // =========================

            if (count($this->batchData) >= 100) {

                $this->insertBatch();
            }

        } catch (\Exception $e) {

            $this->errorCount++;

            Log::error(
                "MIS Import Row Failed: " . $e->getMessage()
            );
        }
    }

    /**
     * Destructor flush remaining rows
     */
    public function __destruct()
    {
        $this->insertBatch();
    }

    /**
     * Bulk UPSERT
     */
    protected function insertBatch()
    {
        try {

            if (empty($this->batchData)) {
                return;
            }

            DB::table($this->tableName)->upsert(

                $this->batchData,

                // Unique keys
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

            Log::info(
                "Inserted batch: " . count($this->batchData)
            );

            $this->batchData = [];

        } catch (\Exception $e) {

            Log::error(
                "Batch Insert Failed: " . $e->getMessage()
            );
        }
    }

    /**
     * Chunk size
     */
    public function chunkSize(): int
    {
        return 100;
    }

    /**
     * Batch size
     */
    public function batchSize(): int
    {
        return 100;
    }

    /**
     * CSV Settings
     */
    public function getCsvSettings(): array
    {
        return [

            'delimiter' => ',',

            'enclosure' => '"',

            'escape_character' => '\\',

            'input_encoding' => 'UTF-8',
        ];
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

            if (
                strtolower($value) === strtolower($validValue)
            ) {
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

            'errors' => $this->errorCount
        ];
    }
}

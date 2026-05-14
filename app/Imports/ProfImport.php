<?php

namespace App\Imports;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProfImport implements ToCollection, WithHeadingRow
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

                $wardNo = $this->cleanValue(
                    $row['ward_no'] ?? $row['ward'] ?? null
                );

                $assessment = $this->cleanValue(
                    $row['assessment'] ?? null
                );

                // Skip empty rows
                if (!$wardNo && !$assessment) {
                    continue;
                }

                $data = [

                    'ward_no' => $wardNo,

                    'road_name' => $this->cleanValue(
                        $row['road_name'] ?? null
                    ),

                    'assessment' => $assessment,

                    'prof_tax_assessment' => $this->cleanValue(
                        $row['prof_tax_assessment'] ?? null
                    ),

                    'old_prof_tax_assessment' => $this->cleanValue(
                        $row['old_prof_tax_assessment'] ?? null
                    ),

                    'name' => $this->cleanValue(
                        $row['name'] ?? null
                    ),

                    'door_no' => $this->cleanValue(
                        $row['door_no'] ?? null
                    ),

                    'phone' => $this->cleanValue(
                        $row['phone'] ?? null
                    ),

                    'category' => $this->cleanValue(
                        $row['category'] ?? null
                    ),

                    'bussiness_name' => $this->cleanValue(
                        $row['bussiness_name'] ?? null
                    ),

                    'hlafyear_tax' => $this->cleanNumeric(
                        $row['hlafyear_tax'] ?? null
                    ),

                    'blalance' => $this->cleanNumeric(
                        $row['blalance'] ?? null
                    ),

                    'updated_at' => now(),
                ];

                // Check existing record
                $existing = DB::table($this->tableName)
                    ->where('assessment', $assessment)
                    ->where('ward_no', $wardNo)
                    ->first();

                if ($existing) {

                    DB::table($this->tableName)
                        ->where('id', $existing->id)
                        ->update($data);

                    $this->updatedCount++;

                } else {

                    $data['created_at'] = now();

                    DB::table($this->tableName)
                        ->insert($data);

                    $this->importedCount++;
                }

            } catch (\Exception $e) {

                $this->errorCount++;

                Log::error(
                    "Prof Import Error Row {$index}: " . $e->getMessage()
                );

                continue;
            }
        }

        Log::info(
            "✅ PROF Import Completed - Imported: {$this->importedCount}, Updated: {$this->updatedCount}, Errors: {$this->errorCount}"
        );
    }

    private function cleanValue($value)
    {
        if (is_null($value)) {
            return null;
        }

        $value = is_string($value)
            ? trim($value)
            : $value;

        if (
            $value === '' ||
            $value === 'NULL' ||
            $value === 'null'
        ) {
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

        if (is_string($value)) {
            $value = preg_replace('/[^\d.-]/', '', $value);
        }

        return is_numeric($value)
            ? (float) $value
            : null;
    }

    public function getImportStats(): array
    {
        return [
            'imported' => $this->importedCount,
            'updated' => $this->updatedCount,
            'errors' => $this->errorCount,
        ];
    }
}

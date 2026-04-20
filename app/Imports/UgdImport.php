<?php

namespace App\Imports;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class UgdImport implements ToCollection, WithHeadingRow
{
    protected string $tableName;
    protected int $corporationId;
    protected int $importedCount = 0;
    protected int $updatedCount = 0;
    protected int $errorCount = 0;

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
                    $row['ward_no'] ?? $row['ward'] ?? $row['ward_number'] ?? null
                );

                $assessment = $this->cleanValue(
                    $row['assessment'] ?? $row['assessment_no'] ?? $row['assessment_number'] ?? null
                );

                // Skip if essential fields missing
                if (!$wardNo || !$assessment) {
                    $this->errorCount++;
                    Log::warning("Skipping row {$index} - missing ward_no or assessment");
                    continue;
                }

                // Prepare clean, mapped data
                $data = [
                    'corporation_id'     => $this->corporationId,
                    'ward_no'            => $wardNo,
                    'assessment'         => $assessment,
                    'road_name'          => $this->cleanValue($row['road_name'] ?? $row['street'] ?? null),
                    'ugd_no'             => $this->cleanValue($row['ugd_no'] ?? $row['connection_no'] ?? null),
                    'old_ugd_no'         => $this->cleanValue($row['old_ugd_no'] ?? $row['old_connection'] ?? null),
                    'owner_name'         => $this->cleanValue($row['owner_name'] ?? $row['owner'] ?? null),
                    'old_door_no'        => $this->cleanValue($row['old_door_no'] ?? $row['door_no'] ?? null),
                    'new_door_no'        => $this->cleanValue($row['new_door_no'] ?? $row['doorno'] ?? null),
                    'phone_number'       => $this->cleanValue($row['phone_number'] ?? $row['mobile'] ?? null),
                    'slap_rate'          => $this->cleanValue($row['slap_rate'] ?? $row['rate'] ?? null),
                    'balance'            => $this->cleanNumeric($row['balance'] ?? $row['due_balance'] ?? null),
                    'usage'              => $this->cleanEnum($row['usage'] ?? null, 'usage'),
                    'slab_description'   => $this->cleanEnum($row['slab_description'] ?? null, 'slab'),
                    'DBC_type'           => $this->cleanEnum($row['dbc_type'] ?? $row['type'] ?? null, 'dbc'),

                    // 🧾 Tax-related columns
                    'tax_year'           => $this->cleanValue($row['tax_year'] ?? $row['year'] ?? null),
                    'ugd_tax_amount'     => $this->cleanNumeric($row['ugd_tax_amount'] ?? $row['tax_amount'] ?? null),
                    'ugd_tax_due'        => $this->cleanNumeric($row['ugd_tax_due'] ?? $row['due'] ?? null),
                    'ugd_tax_paid'       => $this->cleanNumeric($row['ugd_tax_paid'] ?? $row['paid'] ?? null),
                    'ugd_tax_paid_date'  => $this->cleanDate($row['ugd_tax_paid_date'] ?? $row['paid_date'] ?? null),
                    'payment_mode'       => $this->cleanValue($row['payment_mode'] ?? $row['mode'] ?? null),
                    'receipt_number'     => $this->cleanValue($row['receipt_number'] ?? $row['receipt'] ?? null),
                    'due_date'           => $this->cleanDate($row['due_date'] ?? $row['tax_due_date'] ?? null),
                    'status'             => $this->cleanEnum($row['status'] ?? null, 'status'),
                    'remarks'            => $this->cleanValue($row['remarks'] ?? $row['note'] ?? null),

                    'updated_at'         => now(),
                ];

                // 🔍 Check if record already exists
                $existing = DB::table($this->tableName)
                    ->where('corporation_id', $this->corporationId)
                    ->where('ward_no', $wardNo)
                    ->where('assessment', $assessment)
                    ->first();

                if ($existing) {
                    DB::table($this->tableName)
                        ->where('id', $existing->id)
                        ->update($data);
                    $this->updatedCount++;
                } else {
                    $data['created_at'] = now();
                    DB::table($this->tableName)->insert($data);
                    $this->importedCount++;
                }
            } catch (\Exception $e) {
                $this->errorCount++;
                Log::error("Row {$index} failed for {$this->tableName}: " . $e->getMessage());
                continue;
            }
        }

        Log::info("✅ UGD Import Summary: Imported={$this->importedCount}, Updated={$this->updatedCount}, Errors={$this->errorCount}");
    }

    /* -----------------------------
     *  CLEANING HELPERS
     * ----------------------------- */
    private function cleanValue($value)
    {
        if (is_null($value)) return null;
        $value = trim((string)$value);
        return ($value === '' || strtolower($value) === 'null') ? null : $value;
    }

    private function cleanNumeric($value)
    {
        $value = $this->cleanValue($value);
        if (is_null($value)) return null;
        $value = preg_replace('/[^\d.-]/', '', $value);
        return is_numeric($value) ? (float)$value : null;
    }

    private function cleanDate($value)
    {
        $value = $this->cleanValue($value);
        if (is_null($value)) return null;
        try {
            return date('Y-m-d', strtotime($value));
        } catch (\Exception) {
            return null;
        }
    }

    private function cleanEnum($value, $type)
    {
        $value = $this->cleanValue($value);
        if (is_null($value)) return null;

        $enums = [
            'usage' => ['Residential', 'Commercial', 'Industrial', 'Institutional', 'Vacant'],
            'dbc'   => ['Owner', 'Tenant', 'Mixed', 'Government', 'Others'],
            'slab'  => ['Domestic', 'Non-Domestic'],
            'status'=> ['Active', 'Inactive']
        ];

        foreach ($enums[$type] ?? [] as $valid) {
            if (strcasecmp($valid, $value) === 0) return $valid;
        }

        return null;
    }

    /* -----------------------------
     *  STATISTICS
     * ----------------------------- */
    public function getImportStats(): array
    {
        return [
            'imported' => $this->importedCount,
            'updated'  => $this->updatedCount,
            'errors'   => $this->errorCount,
        ];
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Corporation;
use App\Models\Ward;

class CommissionerController extends Controller
{
    /**
     * Display the commissioner dashboard with all data.
     */
    public function dashboard()
    {
        // Increase execution limits to prevent timeout
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '1024M');
        set_time_limit(300);

        $user = Auth::guard('corporation')->user();

        if (!$user) {
            return redirect()->route('corporation.login');
        }

        $corporation = Corporation::find($user->corporation_id);

        if (!$corporation) {
            return back()->with('error', 'Corporation not found');
        }

        // Get wards with limit to prevent timeout
        $wards = Ward::where('corporation_id', $corporation->id)
            ->where('status', 'active')
            ->limit(100) // Limit to 100 wards max
            ->get();

        $ward_count = $wards->count();

        // Get MIS count
        $mis_count = 0;
        $misTable = "mis_corporation_{$corporation->id}";
        if (Schema::hasTable($misTable)) {
            $mis_count = DB::table($misTable)->count();
        }

        // Initialize totals
        $total_buildings = 0;
        $total_area_variation = 0;
        $total_usage_variation = 0;

        // Build collections
        $collections = [];
        $chartData = [];

        foreach ($wards as $wardlist) {
            // Skip if table doesn't exist
            $polygontable = $this->getPolygonTable($corporation->id, $wardlist->ward_no, $wardlist->zone);

            if (!$polygontable || !Schema::hasTable($polygontable)) {
                continue;
            }

            // Get building count
            $buildingCount = DB::table($polygontable)->count();

            if ($buildingCount == 0) {
                continue;
            }

            // Calculate variations for this ward
            $variationStats = $this->calculateWardVariations($corporation->id, $wardlist->zone, $wardlist->ward_no);

            // Accumulate totals
            $total_buildings += $buildingCount;
            $total_area_variation += $variationStats['area_variation_count'];
            $total_usage_variation += $variationStats['usage_variation_count'];

            // Prepare chart data
            $chartData[] = [
                'ward' => "Ward {$wardlist->ward_no}",
                'ward_no' => $wardlist->ward_no,
                'area_variation' => $variationStats['area_variation_count'],
                'usage_variation' => $variationStats['usage_variation_count'],
                'total_buildings' => $buildingCount,
                'areaVariationCount' => $variationStats['area_variation_count'],
                'usageVariationCount' => $variationStats['usage_variation_count'],
                'areaVariationPercentage' => $variationStats['area_variation_percentage'],
                'usageVariationPercentage' => $variationStats['usage_variation_percentage']
            ];

            $data = [
                "zone" => $wardlist->zone,
                "ward_no" => $wardlist->ward_no,
                "buildingCount" => $buildingCount,
                "surveyedBuildingCount" => $variationStats['surveyed_count'],
                "areaVariationCount" => $variationStats['area_variation_count'],
                "usageVariationCount" => $variationStats['usage_variation_count'],
                "areaVariationPercentage" => $variationStats['area_variation_percentage'],
                "usageVariationPercentage" => $variationStats['usage_variation_percentage'],
            ];

            $collections[] = $data;
        }

        return view('corporation.dashboard', [
            "corporation" => $corporation,
            "ward_count" => $ward_count,
            "mis_count" => $mis_count,
            "collections" => $collections,
            "total_buildings" => $total_buildings,
            "total_area_variation" => $total_area_variation,
            "total_usage_variation" => $total_usage_variation,
            "area_variation_percentage" => $total_buildings > 0 ? round(($total_area_variation / $total_buildings) * 100, 1) : 0,
            "usage_variation_percentage" => $total_buildings > 0 ? round(($total_usage_variation / $total_buildings) * 100, 1) : 0,
            "chartData" => $chartData
        ]);
    }

    private function calculateWardVariations($corporationId, $zone, $wardNo)
    {
        $zone = strtolower(trim($zone));
        $wardNo = (int)$wardNo;

        $polygonsTableName = "polygon_{$corporationId}_{$zone}_{$wardNo}";
        $pointDataTableName = "pointdata_{$corporationId}_{$zone}_{$wardNo}";
        $misTableName = "mis_corporation_{$corporationId}";

        // Check if required tables exist
        if (!Schema::hasTable($polygonsTableName) || !Schema::hasTable($pointDataTableName)) {
            return [
                'area_variation_count' => 0,
                'usage_variation_count' => 0,
                'surveyed_count' => 0,
                'area_variation_percentage' => 0,
                'usage_variation_percentage' => 0
            ];
        }

        // Get polygons with their sqfeet
        $polygons = DB::table($polygonsTableName)
            ->select('gisid', 'sqfeet')
            ->get();

        // Get point data with assessment info
        $pointDatas = DB::table($pointDataTableName . ' as pd')
            ->leftJoin($misTableName . ' as mis', 'pd.assessment', '=', 'mis.assessment')
            ->select(
                'pd.point_gisid',
                'pd.assessment',
                'pd.qcsqfeet',
                'pd.bill_usage',
                'mis.plot_area'
            )
            ->get();

        // Group point data by GIS ID
        $pointDataByGisid = [];
        foreach ($pointDatas as $pointData) {
            $gisid = $pointData->point_gisid;
            if (!isset($pointDataByGisid[$gisid])) {
                $pointDataByGisid[$gisid] = [];
            }
            $pointDataByGisid[$gisid][] = $pointData;
        }

        $areaVariationCount = 0;
        $usageVariationCount = 0;
        $validBuildingsCount = 0;
        $surveyedBuildingsCount = 0;

        foreach ($polygons as $polygon) {
            $gisid = $polygon->gisid;
            $buildingArea = floatval($polygon->sqfeet ?? 0);

            if ($buildingArea <= 0) {
                continue;
            }

            $validBuildingsCount++;

            // Check if this building has point data (surveyed)
            if (isset($pointDataByGisid[$gisid])) {
                $surveyedBuildingsCount++;

                $assessmentArea = 0;
                $hasUsageMismatch = false;
                $pointUsage = null;

                foreach ($pointDataByGisid[$gisid] as $pointData) {
                    // Calculate assessment area
                    $pointArea = 0;
                    if (isset($pointData->qcsqfeet) && $pointData->qcsqfeet > 0) {
                        $pointArea = floatval($pointData->qcsqfeet);
                    } elseif (isset($pointData->plot_area) && $pointData->plot_area > 0) {
                        $pointArea = floatval($pointData->plot_area);
                    }
                    $assessmentArea += $pointArea;

                    // Get usage for comparison
                    $currentUsage = $pointData->bill_usage ?? null;
                    if ($currentUsage) {
                        $pointUsage = $currentUsage;
                    }
                }

                // AREA VARIATION: Compare building sqfeet with assessment area
                // Variation if difference is more than 5% or absolute difference > 10 sq ft
                $areaDiff = abs($buildingArea - $assessmentArea);
                $variationThreshold = max($buildingArea * 0.05, 10); // 5% or at least 10 sq ft

                if ($areaDiff > $variationThreshold && $assessmentArea > 0) {
                    $areaVariationCount++;
                }

                // USAGE VARIATION: Check if building has mixed usage or different from standard
                // For now, we consider any non-residential as variation (customize as needed)
                if ($pointUsage && !in_array(strtoupper(trim($pointUsage)), ['RESIDENTIAL', 'R', 'RES'])) {
                    $usageVariationCount++;
                }
            }
        }

        return [
            'area_variation_count' => $areaVariationCount,
            'usage_variation_count' => $usageVariationCount,
            'surveyed_count' => $surveyedBuildingsCount,
            'area_variation_percentage' => $validBuildingsCount > 0 ? round(($areaVariationCount / $validBuildingsCount) * 100, 1) : 0,
            'usage_variation_percentage' => $validBuildingsCount > 0 ? round(($usageVariationCount / $validBuildingsCount) * 100, 1) : 0,
        ];
    }

    /**
     * Export ward data to Excel with building variations
     */
    public function mapDownloadExcel($ward_no)
    {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '512M');

        $user = Auth::guard('corporation')->user();

        if (!$user) {
            return redirect()->route('corporation.login');
        }

        $warddetail = Ward::where('corporation_id', $user->corporation_id)
            ->where('ward_no', $ward_no)
            ->first();

        if (!$warddetail) {
            return back()->with('error', 'Ward not found');
        }

        $zone = strtolower(trim($warddetail->zone));
        $wardNo = (int)$warddetail->ward_no;
        $corp = (int)$warddetail->corporation_id;

        $polygonsTableName = "polygon_{$corp}_{$zone}_{$wardNo}";
        $pointDataTableName = "pointdata_{$corp}_{$zone}_{$wardNo}";
        $misTableName = "mis_corporation_{$corp}";

        if (!Schema::hasTable($polygonsTableName)) {
            return back()->with('error', 'Building data not found for this ward');
        }

        // Get polygons with their data
        $polygons = DB::table($polygonsTableName)
            ->select('gisid', 'sqfeet')
            ->get();

        // Get point data grouped by GIS ID
        $pointDataByGisid = [];

        if (Schema::hasTable($pointDataTableName)) {
            $pointDatas = DB::table($pointDataTableName . ' as pd')
                ->leftJoin($misTableName . ' as mis', 'pd.assessment', '=', 'mis.assessment')
                ->select(
                    'pd.point_gisid',
                    'pd.assessment',
                    'pd.qcsqfeet',
                    'pd.bill_usage',
                    'pd.qcusage',
                    'mis.plot_area',
                    'mis.owner_name',
                    'mis.address',
                    'mis.road_name'
                )
                ->get();

            foreach ($pointDatas as $pointData) {
                $gisid = $pointData->point_gisid;
                if (!isset($pointDataByGisid[$gisid])) {
                    $pointDataByGisid[$gisid] = [];
                }
                $pointDataByGisid[$gisid][] = $pointData;
            }
        }

        $excelData = [];
        $rowNumber = 1;

        $excelData[] = [
            'S.No',
            'GIS ID',
            'Building Area (sq ft)',
            'Assessment Area (sq ft)',
            'Area Difference',
            'Area Variation %',
            'Area Status',
            'Building Usage',
            'Assessment Usage',
            'Usage Status',
            'Owner Name',
            'Address',
            'Road Name',
            'Assessment ID'
        ];

        $buildingsWithAreaVariation = 0;
        $buildingsWithUsageVariation = 0;
        $totalBuildingArea = 0;
        $totalAssessmentArea = 0;

        foreach ($polygons as $polygon) {
            $gisid = $polygon->gisid;
            $buildingArea = floatval($polygon->sqfeet ?? 0);
            $totalBuildingArea += $buildingArea;

            $assessmentArea = 0;
            $assessmentIds = [];
            $assessmentUsage = null;
            $ownerName = '';
            $address = '';
            $roadName = '';

            if (isset($pointDataByGisid[$gisid])) {
                foreach ($pointDataByGisid[$gisid] as $pointData) {
                    // Calculate assessment area
                    $pointArea = 0;
                    if (isset($pointData->qcsqfeet) && $pointData->qcsqfeet > 0) {
                        $pointArea = floatval($pointData->qcsqfeet);
                    } elseif (isset($pointData->plot_area) && $pointData->plot_area > 0) {
                        $pointArea = floatval($pointData->plot_area);
                    }
                    $assessmentArea += $pointArea;

                    // Get assessment ID
                    if (isset($pointData->assessment)) {
                        $assessmentIds[] = $pointData->assessment;
                    }

                    // Get usage
                    $usage = $pointData->bill_usage ?? $pointData->qcusage ?? null;
                    if ($usage && !$assessmentUsage) {
                        $assessmentUsage = $usage;
                    }

                    // Get owner info
                    if (empty($ownerName) && isset($pointData->owner_name)) {
                        $ownerName = $pointData->owner_name;
                        $address = $pointData->address ?? '';
                        $roadName = $pointData->road_name ?? '';
                    }
                }
            }

            $totalAssessmentArea += $assessmentArea;

            // Calculate area variation
            $areaDiff = $buildingArea - $assessmentArea;
            $areaVariationPercent = $buildingArea > 0 ? round(($areaDiff / $buildingArea) * 100, 2) : 0;

            // Determine area status (10% threshold)
            $areaStatus = 'MATCH';
            if (abs($areaVariationPercent) > 10) {
                $areaStatus = abs($areaDiff) > $assessmentArea ? 'VARIATION (Higher)' : 'VARIATION (Lower)';
                $buildingsWithAreaVariation++;
            }

            // Determine usage status
            $usageStatus = 'N/A';
            $buildingUsage = 'Residential'; // Default assumption

            if ($assessmentUsage) {
                $usageStatus = (strtoupper(trim($assessmentUsage)) == 'RESIDENTIAL' ||
                    strtoupper(trim($assessmentUsage)) == 'R') ? 'MATCH' : 'VARIATION';
                if ($usageStatus == 'VARIATION') {
                    $buildingsWithUsageVariation++;
                }
            }

            $excelData[] = [
                $rowNumber++,
                $gisid,
                number_format($buildingArea, 2),
                number_format($assessmentArea, 2),
                number_format($areaDiff, 2),
                $areaVariationPercent . '%',
                $areaStatus,
                $buildingUsage,
                $assessmentUsage ?? 'Not Assessed',
                $usageStatus,
                $ownerName ?: 'N/A',
                $address ?: 'N/A',
                $roadName ?: 'N/A',
                implode(', ', $assessmentIds) ?: 'N/A'
            ];
        }

        $totalBuildings = count($polygons);

        return $this->generateExcel($excelData, $warddetail, [
            'totalBuildings' => $totalBuildings,
            'areaVariationCount' => $buildingsWithAreaVariation,
            'usageVariationCount' => $buildingsWithUsageVariation,
            'totalBuildingArea' => round($totalBuildingArea, 2),
            'totalAssessmentArea' => round($totalAssessmentArea, 2),
            'wardName' => "Ward {$warddetail->ward_no}",
            'zone' => $warddetail->zone,
            'corporationName' => $user->name ?? 'Corporation'
        ]);
    }

    private function generateExcel($data, $ward, $summary)
    {
        $filename = "ward_{$ward->ward_no}_building_variations_" . date('Ymd_His') . ".xls";

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        echo '<html>';
        echo '<head><meta charset="UTF-8">';
        echo '<title>Ward ' . $ward->ward_no . ' Building Variations Report</title>';
        echo '<style>';
        echo 'body { font-family: Arial, sans-serif; }';
        echo 'th { background-color: #4472C4; color: white; border: 1px solid #000; padding: 8px; font-size: 12px; }';
        echo 'td { border: 1px solid #ccc; padding: 6px; font-size: 11px; }';
        echo '.summary-table { margin-bottom: 20px; border-collapse: collapse; width: 100%; }';
        echo '.summary-table td { padding: 8px; font-size: 12px; }';
        echo '.header { font-size: 18px; font-weight: bold; margin-bottom: 20px; }';
        echo '.subheader { font-size: 14px; margin-bottom: 20px; color: #666; }';
        echo '.variation-match { background-color: #C6EFCE; }';
        echo '.variation-mismatch { background-color: #FFC7CE; }';
        echo '.title { background-color: #102C57; color: white; padding: 10px; }';
        echo '</style></head><body>';

        echo '<div class="header">';
        echo '<h2>' . htmlspecialchars($summary['corporationName']) . '</h2>';
        echo '<h3>Building Variation Report - ' . htmlspecialchars($summary['wardName']) . ' (' . htmlspecialchars($summary['zone']) . ' Zone)</h3>';
        echo '</div>';
        echo '<div class="subheader">Generated on: ' . date('d-m-Y H:i:s') . '</div>';

        echo '<h3>Summary Statistics</h3>';
        echo '<table class="summary-table" border="1" cellpadding="5" cellspacing="0">';
        echo '<tr style="background-color: #E6E6E6;"><td width="50%"><strong>Total Buildings:</strong></td><td>' . number_format($summary['totalBuildings']) . '</td></tr>';
        echo '<tr><td><strong>Buildings with Area Variation:</strong></td><td>' . $summary['areaVariationCount'] . ' (' . round(($summary['areaVariationCount'] / max(1, $summary['totalBuildings'])) * 100, 2) . '%)</td></tr>';
        echo '<tr style="background-color: #E6E6E6;"><td><strong>Buildings with Usage Variation:</strong></td><td>' . $summary['usageVariationCount'] . ' (' . round(($summary['usageVariationCount'] / max(1, $summary['totalBuildings'])) * 100, 2) . '%)</td></tr>';
        echo '<tr><td><strong>Total Building Area:</strong></td><td>' . number_format($summary['totalBuildingArea'], 2) . ' sq ft</td></tr>';
        echo '<tr style="background-color: #E6E6E6;"><td><strong>Total Assessment Area:</strong></td><td>' . number_format($summary['totalAssessmentArea'], 2) . ' sq ft</td></tr>';
        echo '<tr><td><strong>Total Area Variation:</strong></td><td>' . number_format($summary['totalBuildingArea'] - $summary['totalAssessmentArea'], 2) . ' sq ft</td></tr>';
        echo '</table><br><br>';

        echo '<h3>Detailed Building Data</h3>';
        echo '<table border="1" cellpadding="5" cellspacing="0" width="100%">';

        // Header
        echo '<tr>';
        foreach ($data[0] as $header) {
            echo '<th>' . htmlspecialchars($header) . '</th>';
        }
        echo '</tr>';

        // Data rows
        for ($i = 1; $i < count($data); $i++) {
            $row = $data[$i];
            $rowClass = '';
            if (strpos($row[6], 'VARIATION') !== false || $row[9] == 'VARIATION') {
                $rowClass = 'class="variation-mismatch"';
            } elseif ($row[6] == 'MATCH' && $row[9] == 'MATCH') {
                $rowClass = 'class="variation-match"';
            }
            echo '<tr ' . $rowClass . '>';
            foreach ($row as $value) {
                echo '<td>' . htmlspecialchars($value) . '</td>';
            }
            echo '</tr>';
        }
        echo '</table>';

        echo '<br><br>';
        echo '<table border="0" cellpadding="5">';
        echo '<tr><td style="background-color: #C6EFCE; border:1px solid #000; width:20px;">&nbsp;</td><td><strong>Match:</strong> No significant variations found</td></tr>';
        echo '<tr><td style="background-color: #FFC7CE; border:1px solid #000;">&nbsp;</td><td><strong>Variation:</strong> Area (>10%) or Usage mismatch detected</td></tr>';
        echo '</table>';

        echo '</body></html>';
        exit;
    }

    public function mapView($ward_no)
    {
        $userId = Auth::guard('corporation')->user();

        if (!$userId) {
            return redirect()->route('corporation.login');
        }

        $warddetail = Ward::where('corporation_id', $userId->corporation_id)
            ->where('ward_no', $ward_no)
            ->first();

        if (!$warddetail) {
            return back()->with('error', 'Ward not found');
        }

        $zone = strtolower(trim($warddetail->zone));
        $wardNo = (int)$warddetail->ward_no;
        $corp = (int)$warddetail->corporation_id;

        // Table names
        $polygonsTableName = "polygon_{$corp}_{$zone}_{$wardNo}";
        $linesTableName = "line_{$corp}_{$zone}_{$wardNo}";

        $pointDataTable = $this->getPointDataTable($corp, $wardNo, $zone);
        $polygonDataTable = $this->getPolygonDataTable($corp, $wardNo, $zone);
        $shopTableName = "shopdata_{$corp}_{$zone}_{$wardNo}";

        // Get polygons (buildings)
        $polygons = Schema::hasTable($polygonsTableName)
            ? DB::table($polygonsTableName)->select('gisid', 'coordinates', 'sqfeet')->get()
            : [];

        // Get lines (roads)
        $lines = Schema::hasTable($linesTableName)
            ? DB::table($linesTableName)->select('gisid', 'coordinates')->get()
            : [];

        // Get polygon data if table exists
        $polygonDatas = collect();
        if ($polygonDataTable && Schema::hasTable($polygonDataTable)) {
            $polygonDatas = DB::table($polygonDataTable)
                ->select(
                    'id',
                    'gisid',
                    'number_bill',
                    'number_floor',
                    DB::raw('Percentage as floor_percentage'),
                    'building_usage',
                    'construction_type',
                    'road_name',
                    'ugd',
                    'basement',
                    'water_connection',
                    'image',
                    'building_type',
                    'image2',
                    'remarks'
                )
                ->get();
        }

        // Get point data if table exists
        $pointDatas = collect();
        if ($pointDataTable && Schema::hasTable($pointDataTable)) {
            $pointDatas = DB::table($pointDataTable)
                ->select(
                    'id',
                    'building_data_id',
                    'point_gisid',
                    'assessment',
                    'old_assessment',
                    'owner_name',
                    'present_owner_name',
                    'floor',
                    'bill_usage',
                    'phone_number',
                    'old_door_no',
                    'new_door_no',
                    'remarks',
                    'water_tax',
                    'zone',
                    'qcusage',
                    'qcsqfeet'
                )
                ->get();
        }

        // Get shop data if table exists
        $shopDatas = collect();
        if (Schema::hasTable($shopTableName)) {
            $shopDatas = DB::table($shopTableName)->get();
        }

        // Group shops by point_data_id
        $shopsGrouped = $shopDatas->groupBy('point_data_id');

        // Attach shops to pointdata
        foreach ($pointDatas as $pointdata) {
            $pointdata->shops = $shopsGrouped[$pointdata->id] ?? collect();
        }

        // Group pointdata by point_gisid
        $pointGrouped = $pointDatas->groupBy('point_gisid');

        // Attach pointdata to polygondata
        foreach ($polygonDatas as $polygondata) {
            $polygondata->pointdata = $pointGrouped[$polygondata->gisid] ?? collect();
            $polygondata->total_points = count($polygondata->pointdata);
            $polygondata->total_shops = $polygondata->pointdata->sum(function ($point) {
                return count($point->shops);
            });
        }

        $ward = $warddetail;

        return view('corporation.ward-map', compact('ward', 'polygons', 'lines', 'polygonDatas'));
    }

    private function getPointDataTable($corporationId, $wardNo, $zone)
    {
        $zone = trim(strtolower($zone));
        $tableName = "pointdata_{$corporationId}_{$zone}_{$wardNo}";
        return Schema::hasTable($tableName) ? $tableName : null;
    }

    private function getPolygonDataTable($corporationId, $wardNo, $zone)
    {
        $zone = trim(strtolower($zone));
        $tableName = "polygondata_{$corporationId}_{$zone}_{$wardNo}";
        return Schema::hasTable($tableName) ? $tableName : null;
    }

    private function getPolygonTable($corporationId, $wardNo, $zone)
    {
        $zone = trim(strtolower($zone));
        $tableName = "polygon_{$corporationId}_{$zone}_{$wardNo}";
        return Schema::hasTable($tableName) ? $tableName : null;
    }

    private function getLineTable($corporationId, $wardNo, $zone)
    {
        $zone = trim(strtolower($zone));
        $tableName = "line_{$corporationId}_{$zone}_{$wardNo}";
        return Schema::hasTable($tableName) ? $tableName : null;
    }
}

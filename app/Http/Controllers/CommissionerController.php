<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Corporation;
use App\Models\Ward;
use SebastianBergmann\CodeCoverage\Util\Percentage;

class CommissionerController extends Controller
{
      public function dashboard()
    {
        $user = Auth::guard('corporation')->user();

        if (!$user) {
            return redirect()->route('corporation.login');
        }

        $corporation = Corporation::find($user->corporation_id);

        if (!$corporation) {
            return back()->with('error', 'Corporation not found');
        }

        return Cache::remember(
            'corporation_dashboard_' . $corporation->id,
            300,
            function () use ($corporation, $user) {

                /*
                |--------------------------------------------------------------------------
                | LOAD ALL TABLE NAMES ONCE
                |--------------------------------------------------------------------------
                */
                $tableNames = collect(DB::select("SHOW TABLES"))
                    ->map(function ($table) {
                        return array_values((array)$table)[0];
                    })
                    ->flip();

                /*
                |--------------------------------------------------------------------------
                | TABLE STATISTICS (FAST COUNTS)
                |--------------------------------------------------------------------------
                */
                $tableStats = DB::table('information_schema.tables')
                    ->select('table_name', 'table_rows')
                    ->where('table_schema', DB::raw('DATABASE()'))
                    ->get()
                    ->keyBy('table_name');

                /*
                |--------------------------------------------------------------------------
                | WARDS
                |--------------------------------------------------------------------------
                */
                $wards = Ward::where('corporation_id', $corporation->id)
                    ->where('status', 'active')
                    ->get();

                $ward_count = $wards->count();

                $wardGroups = $wards->groupBy('zone');

                /*
                |--------------------------------------------------------------------------
                | MIS TABLE
                |--------------------------------------------------------------------------
                */
                $misTable = "mis_corporation_{$corporation->id}";

                $mis_count = isset($tableNames[$misTable])
                    ? ($tableStats[$misTable]->table_rows ?? 0)
                    : 0;

                /*
                |--------------------------------------------------------------------------
                | INITIALIZE
                |--------------------------------------------------------------------------
                */
                $collections = [];
                $zonesWithWards = [];
                $chartData = [];

                $total_buildings = 0;
                $total_area_variation = 0;
                $total_usage_variation = 0;

                /*
                |--------------------------------------------------------------------------
                | LOOP ZONES
                |--------------------------------------------------------------------------
                */
                foreach ($wardGroups as $zone => $wardlists) {

                    $zoneData = [
                        'zone' => $zone,
                        'wards' => []
                    ];

                    foreach ($wardlists as $wardlist) {

                        $pointdatatable = $this->getPointDataTable(
                            $corporation->id,
                            $wardlist->ward_no,
                            $wardlist->zone
                        );

                        $polygondatatable = $this->getPolygonDataTable(
                            $corporation->id,
                            $wardlist->ward_no,
                            $wardlist->zone
                        );

                        $polygontable = $this->getPolygonTable(
                            $corporation->id,
                            $wardlist->ward_no,
                            $wardlist->zone
                        );

                        $roadtable = $this->getLineTable(
                            $corporation->id,
                            $wardlist->ward_no,
                            $wardlist->zone
                        );

                        /*
                        |--------------------------------------------------------------------------
                        | FAST COUNTS USING information_schema
                        |--------------------------------------------------------------------------
                        */
                        $buildingCount = isset($tableStats[$polygontable])
                            ? (int)$tableStats[$polygontable]->table_rows
                            : 0;

                        $surveyedBuildingCount = isset($tableStats[$polygondatatable])
                            ? (int)$tableStats[$polygondatatable]->table_rows
                            : 0;

                        $pointCount = isset($tableStats[$pointdatatable])
                            ? (int)$tableStats[$pointdatatable]->table_rows
                            : 0;

                        $roadCount = isset($tableStats[$roadtable])
                            ? (int)$tableStats[$roadtable]->table_rows
                            : 0;

                        /*
                        |--------------------------------------------------------------------------
                        | MIS COUNT
                        |--------------------------------------------------------------------------
                        */
                        $misCount = 0;

                        if (isset($tableNames[$misTable])) {
                            $misCount = DB::table($misTable)
                                ->where('ward_no', $wardlist->ward_no)
                                ->count();
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | VARIATIONS
                        |--------------------------------------------------------------------------
                        */
                        $variationStats = [
                            'area_variation_count' => 0,
                            'usage_variation_count' => 0,
                            'area_variation_percentage' => 0,
                            'usage_variation_percentage' => 0,
                        ];

                        if ($pointCount > 0) {

                            $variationStats = $this->calculateWardVariations(
                                $corporation->id,
                                $wardlist->zone,
                                $wardlist->ward_no,
                                $tableNames
                            );

                            $total_buildings += $buildingCount;

                            $total_area_variation += $variationStats['area_variation_count'];

                            $total_usage_variation += $variationStats['usage_variation_count'];
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | CHART DATA
                        |--------------------------------------------------------------------------
                        */
                        if ($pointCount > 0) {

                            $chartData[] = [
                                'ward' => 'Ward ' . $wardlist->ward_no,
                                'ward_no' => $wardlist->ward_no,
                                'area_variation' => $variationStats['area_variation_count'],
                                'usage_variation' => $variationStats['usage_variation_count'],
                                'total_buildings' => $buildingCount,
                                'areaVariationCount' => $variationStats['area_variation_count'],
                                'usageVariationCount' => $variationStats['usage_variation_count'],
                                'areaVariationPercentage' => $variationStats['area_variation_percentage'],
                                'usageVariationPercentage' => $variationStats['usage_variation_percentage'],
                            ];
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | COLLECTIONS
                        |--------------------------------------------------------------------------
                        */
                        $collections[] = [

                            "zone" => $wardlist->zone,
                            "ward_no" => $wardlist->ward_no,

                            "pointdatatable" => $pointdatatable,
                            "polygondatatable" => $polygondatatable,
                            "polygontable" => $polygontable,
                            "roadtable" => $roadtable,

                            "buildingCount" => $buildingCount,
                            "surveyedBuildingCount" => $surveyedBuildingCount,
                            "pointCount" => $pointCount,
                            "roadCount" => $roadCount,
                            "misCount" => $misCount,

                            "areaVariationCount" => $variationStats['area_variation_count'],
                            "usageVariationCount" => $variationStats['usage_variation_count'],

                            "areaVariationPercentage" => $variationStats['area_variation_percentage'],
                            "usageVariationPercentage" => $variationStats['usage_variation_percentage'],

                            "hasPointData" => $pointCount > 0,
                        ];

                        /*
                        |--------------------------------------------------------------------------
                        | ZONE DATA
                        |--------------------------------------------------------------------------
                        */
                        $zoneData['wards'][] = [

                            'ward_no' => $wardlist->ward_no,
                            'buildingCount' => $buildingCount,
                            'surveyedCount' => $surveyedBuildingCount,
                            'pointCount' => $pointCount,
                            'roadCount' => $roadCount,
                            'misCount' => $misCount,

                            'areaVariationCount' => $variationStats['area_variation_count'],
                            'usageVariationCount' => $variationStats['usage_variation_count'],

                            'hasPointData' => $pointCount > 0,
                        ];
                    }

                    $zonesWithWards[] = $zoneData;
                }

                /*
                |--------------------------------------------------------------------------
                | RETURN VIEW
                |--------------------------------------------------------------------------
                */
                return view('corporation.dashboard', [

                    "corporation" => $corporation,

                    "ward_count" => $ward_count,

                    "mis_count" => $mis_count,

                    "collections" => $collections,

                    "zonesWithWards" => $zonesWithWards,

                    "total_buildings" => $total_buildings,

                    "total_area_variation" => $total_area_variation,

                    "total_usage_variation" => $total_usage_variation,

                    "area_variation_percentage" =>
                        $total_buildings > 0
                        ? round(($total_area_variation / $total_buildings) * 100, 1)
                        : 0,

                    "usage_variation_percentage" =>
                        $total_buildings > 0
                        ? round(($total_usage_variation / $total_buildings) * 100, 1)
                        : 0,

                    "chartData" => $chartData
                ]);
            }
        );
    }

    /**
     * Calculate Ward Variations
     */
    private function calculateWardVariations(
        $corporationId,
        $zone,
        $wardNo,
        $tableNames
    ) {

        $zone = strtolower(trim($zone));
        $wardNo = (int)$wardNo;

        $polygonsTableName = "polygon_{$corporationId}_{$zone}_{$wardNo}";
        $polygonDataTableName = "polygondata_{$corporationId}_{$zone}_{$wardNo}";
        $pointDataTableName = "pointdata_{$corporationId}_{$zone}_{$wardNo}";
        $misTableName = "mis_corporation_{$corporationId}";

        /*
        |--------------------------------------------------------------------------
        | TABLE CHECKS
        |--------------------------------------------------------------------------
        */
        if (
            !isset($tableNames[$polygonsTableName]) ||
            !isset($tableNames[$pointDataTableName])
        ) {

            return [
                'area_variation_count' => 0,
                'usage_variation_count' => 0,
                'area_variation_percentage' => 0,
                'usage_variation_percentage' => 0
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | LOAD POLYGONS
        |--------------------------------------------------------------------------
        */
        $polygons = DB::table($polygonsTableName)
            ->select('gisid', 'sqfeet')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | LOAD POLYGON DATA
        |--------------------------------------------------------------------------
        */
        $polygonDatas = collect();

        if (isset($tableNames[$polygonDataTableName])) {

            $polygonDatas = DB::table($polygonDataTableName)
                ->select(
                    'gisid',
                    'number_floor',
                    'basement',
                    'building_usage'
                )
                ->get()
                ->keyBy('gisid');
        }

        /*
        |--------------------------------------------------------------------------
        | LOAD POINT DATA
        |--------------------------------------------------------------------------
        */
        $pointDatas = DB::table($pointDataTableName . ' as pd')
            ->leftJoin(
                $misTableName . ' as mis',
                'pd.assessment',
                '=',
                'mis.assessment'
            )
            ->select(
                'pd.point_gisid',
                'pd.qcsqfeet',
                'pd.bill_usage',
                'mis.plot_area'
            )
            ->get()
            ->groupBy('point_gisid');

        /*
        |--------------------------------------------------------------------------
        | COUNTERS
        |--------------------------------------------------------------------------
        */
        $areaVariationCount = 0;
        $usageVariationCount = 0;
        $validBuildingsCount = 0;

        /*
        |--------------------------------------------------------------------------
        | PROCESS
        |--------------------------------------------------------------------------
        */
        foreach ($polygons as $polygon) {

            $gisid = $polygon->gisid;

            $polygonSqfeet = (float)($polygon->sqfeet ?? 0);

            $polyData = $polygonDatas->get($gisid);

            /*
            |--------------------------------------------------------------------------
            | BUILDING AREA
            |--------------------------------------------------------------------------
            */
            if ($polyData) {

                $numberFloor = (float)($polyData->number_floor ?? 0);

                $basement = (float)($polyData->basement ?? 0);

                $buildingArea = ($numberFloor + $basement) * $polygonSqfeet;

                $buildingUsage = $polyData->building_usage ?? null;

            } else {

                $buildingArea = $polygonSqfeet;

                $buildingUsage = null;
            }

            /*
            |--------------------------------------------------------------------------
            | ASSESSMENT AREA
            |--------------------------------------------------------------------------
            */
            $assessmentArea = 0;

            $hasUsageMismatch = false;

            if (isset($pointDatas[$gisid])) {

                foreach ($pointDatas[$gisid] as $pointData) {

                    $pointArea = 0;

                    if (
                        isset($pointData->qcsqfeet) &&
                        $pointData->qcsqfeet > 0
                    ) {

                        $pointArea = (float)$pointData->qcsqfeet;

                    } elseif (
                        isset($pointData->plot_area) &&
                        $pointData->plot_area > 0
                    ) {

                        $pointArea = (float)$pointData->plot_area;
                    }

                    $assessmentArea += $pointArea;

                    $pointUsage = $pointData->bill_usage ?? null;

                    if (
                        $buildingUsage &&
                        $pointUsage &&
                        strtoupper(trim($buildingUsage))
                        != strtoupper(trim($pointUsage))
                    ) {

                        $hasUsageMismatch = true;
                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | VARIATIONS
            |--------------------------------------------------------------------------
            */
            if ($buildingArea > 0 && $assessmentArea > 0) {

                $validBuildingsCount++;

                $areaDiff = abs($buildingArea - $assessmentArea);

                if ($areaDiff > 1) {
                    $areaVariationCount++;
                }

                if ($hasUsageMismatch) {
                    $usageVariationCount++;
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | RETURN
        |--------------------------------------------------------------------------
        */
        return [

            'area_variation_count' => $areaVariationCount,

            'usage_variation_count' => $usageVariationCount,

            'area_variation_percentage' =>
                $validBuildingsCount > 0
                ? round(($areaVariationCount / $validBuildingsCount) * 100, 1)
                : 0,

            'usage_variation_percentage' =>
                $validBuildingsCount > 0
                ? round(($usageVariationCount / $validBuildingsCount) * 100, 1)
                : 0,
        ];
    }

    /**
     * Export ward data to Excel with building variations
     */
    public function mapDownloadExcel($ward_no)
    {
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
        $polygonDataTableName = "polygondata_{$corp}_{$zone}_{$wardNo}";
        $pointDataTableName = "pointdata_{$corp}_{$zone}_{$wardNo}";
        $misTableName = "mis_corporation_{$corp}";

        if (!Schema::hasTable($polygonsTableName)) {
            return back()->with('error', 'Building data not found for this ward');
        }

        $polygons = DB::table($polygonsTableName)->get();
        $polygonDatas = Schema::hasTable($polygonDataTableName)
            ? DB::table($polygonDataTableName)->get()->keyBy('gisid')
            : collect();

        $pointDatas = collect();
        $pointDataByGisid = [];

        if (Schema::hasTable($pointDataTableName)) {
            $pointDataQuery = DB::table($pointDataTableName . ' as pd')
                ->leftJoin($misTableName . ' as mis', 'pd.assessment', '=', 'mis.assessment')
                ->select(
                    'pd.point_gisid',
                    'pd.assessment',
                    'pd.qcsqfeet',
                    'pd.bill_usage',
                    'pd.qcusage',
                    'mis.plot_area',
                    'mis.owner_name',
                    'mis.road_name'
                );

            $pointDatas = $pointDataQuery->get();

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
            'Building Sq Feet',
            'Number of Floors',
            'Basement',
            'Floor Percentage',
            'Total Building Area',
            'Building Usage',
            'Total Assessment Area',
            'Area Variation',
            'Area Variation Status',
            'Number of Bills',
            'Usage Variation Status',
            'Negative Area Variation',
            'Variation Percentage',
            'Owner Name',
            'Address',
            'Road Name'
        ];

        foreach ($polygons as $polygon) {
            $gisid = $polygon->gisid;
            $polygonSqfeet = floatval($polygon->sqfeet ?? 0);

            $polyData = $polygonDatas->get($gisid);
            $numberFloor = 0;
            $basement = 0;
            $floorPercentage = 100;
            $buildingUsage = null;
            $totalBuildingArea = $polygonSqfeet;

            if ($polyData) {
                $numberFloor = floatval($polyData->number_floor ?? 0);
                $basement = floatval($polyData->basement ?? 0);
                $floorPercentage = floatval($polyData->percentage ?? 100);
                $buildingUsage = $polyData->building_usage ?? null;
                $totalBuildingArea = $polygonSqfeet * ($numberFloor + ($floorPercentage / 100) + $basement);
            }

            $assessmentArea = 0;
            $assessmentCount = 0;
            $hasUsageMismatch = false;
            $ownerName = '';
            $address = '';
            $roadName = '';

            if (isset($pointDataByGisid[$gisid])) {
                $assessmentCount = count($pointDataByGisid[$gisid]);

                foreach ($pointDataByGisid[$gisid] as $pointData) {
                    $pointArea = 0;
                    if (isset($pointData->qcsqfeet) && $pointData->qcsqfeet > 0) {
                        $pointArea = floatval($pointData->qcsqfeet);
                    } elseif (isset($pointData->plot_area) && $pointData->plot_area > 0) {
                        $pointArea = floatval($pointData->plot_area);
                    }
                    $assessmentArea += $pointArea;

                    $pointUsage = $pointData->bill_usage ?? $pointData->qcusage ?? null;
                    if ($buildingUsage && $pointUsage && strtoupper(trim($buildingUsage)) != strtoupper(trim($pointUsage))) {
                        $hasUsageMismatch = true;
                    }

                    if (empty($ownerName) && isset($pointData->owner_name)) {
                        $ownerName = $pointData->owner_name;
                        $address = $pointData->road_name ?? '';
                        $roadName = $pointData->road_name ?? '';
                    }
                }
            }

            $areaVariation = $totalBuildingArea - $assessmentArea;
            $areaVariationAbs = abs($areaVariation);
            $hasAreaVariation = $areaVariationAbs > 1;
            $isNegativeVariation = $areaVariation < 0;
            $variationPercentage = $totalBuildingArea > 0 ? round(($areaVariationAbs / $totalBuildingArea) * 100, 2) : 0;

            $areaStatus = $hasAreaVariation ? 'VARIATION' : 'MATCH';
            $usageStatus = $hasUsageMismatch ? 'VARIATION' : 'MATCH';
            $negativeStatus = $isNegativeVariation ? 'YES' : 'NO';

            $excelData[] = [
                $rowNumber++,
                $gisid,
                $polygonSqfeet,
                $numberFloor,
                $basement,
                $floorPercentage . '%',
                round($totalBuildingArea, 2),
                $buildingUsage ?? 'N/A',
                round($assessmentArea, 2),
                round($areaVariation, 2),
                $areaStatus,
                $assessmentCount,
                $usageStatus,
                $negativeStatus,
                $variationPercentage . '%',
                $ownerName,
                $address,
                $roadName
            ];
        }

        $totalBuildings = count($polygons);
        $buildingsWithAreaVariation = 0;
        $buildingsWithUsageVariation = 0;
        $buildingsWithNegativeVariation = 0;
        $totalBuildingAreaSum = 0;
        $totalAssessmentAreaSum = 0;

        foreach ($excelData as $index => $row) {
            if ($index === 0) continue;
            if ($row[10] === 'VARIATION') $buildingsWithAreaVariation++;
            if ($row[12] === 'VARIATION') $buildingsWithUsageVariation++;
            if ($row[13] === 'YES') $buildingsWithNegativeVariation++;
            $totalBuildingAreaSum += floatval($row[6]);
            $totalAssessmentAreaSum += floatval($row[8]);
        }

        return $this->generateExcel($excelData, $warddetail, [
            'totalBuildings' => $totalBuildings,
            'areaVariationCount' => $buildingsWithAreaVariation,
            'usageVariationCount' => $buildingsWithUsageVariation,
            'negativeVariationCount' => $buildingsWithNegativeVariation,
            'totalBuildingArea' => round($totalBuildingAreaSum, 2),
            'totalAssessmentArea' => round($totalAssessmentAreaSum, 2),
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
        echo 'th { background-color: #4472C4; color: white; border: 1px solid #000; padding: 8px; }';
        echo 'td { border: 1px solid #ccc; padding: 6px; }';
        echo '.summary-table { margin-bottom: 20px; border-collapse: collapse; width: 100%; }';
        echo '.summary-table td { padding: 8px; }';
        echo '.header { font-size: 18px; font-weight: bold; margin-bottom: 20px; }';
        echo '.subheader { font-size: 14px; margin-bottom: 20px; color: #666; }';
        echo '.variation-match { background-color: #C6EFCE; }';
        echo '.variation-mismatch { background-color: #FFC7CE; }';
        echo '</style></head><body>';

        echo '<div class="header">';
        echo '<h2>' . htmlspecialchars($summary['corporationName']) . '</h2>';
        echo '<h3>Building Variation Report - ' . htmlspecialchars($summary['wardName']) . ' (' . htmlspecialchars($summary['zone']) . ' Zone)</h3>';
        echo '</div>';
        echo '<div class="subheader">Generated on: ' . date('d-m-Y H:i:s') . '<br></div>';

        echo '<h3>Summary Statistics</h3>';
        echo '<table class="summary-table" border="1" cellpadding="5" cellspacing="0">';
        echo '<tr style="background-color: #E6E6E6;"><td width="50%"><strong>Total Buildings:</strong></td><td>' . $summary['totalBuildings'] . '</td></tr>';
        echo '<tr><td><strong>Buildings with Area Variation:</strong></td><td>' . $summary['areaVariationCount'] . ' (' . round(($summary['areaVariationCount'] / max(1, $summary['totalBuildings'])) * 100, 2) . '%)</td></tr>';
        echo '<tr style="background-color: #E6E6E6;"><td><strong>Buildings with Usage Variation:</strong></td><td>' . $summary['usageVariationCount'] . ' (' . round(($summary['usageVariationCount'] / max(1, $summary['totalBuildings'])) * 100, 2) . '%)</td></tr>';
        echo '<tr><td><strong>Buildings with Negative Variation:</strong></td><td>' . $summary['negativeVariationCount'] . ' (' . round(($summary['negativeVariationCount'] / max(1, $summary['totalBuildings'])) * 100, 2) . '%)</td></tr>';
        echo '<tr style="background-color: #E6E6E6;"><td><strong>Total Building Area:</strong></td><td>' . number_format($summary['totalBuildingArea'], 2) . ' sq ft</td></tr>';
        echo '<tr><td><strong>Total Assessment Area:</strong></td><td>' . number_format($summary['totalAssessmentArea'], 2) . ' sq ft</td></tr>';
        echo '<tr style="background-color: #E6E6E6;"><td><strong>Total Area Variation:</strong></td><td>' . number_format($summary['totalBuildingArea'] - $summary['totalAssessmentArea'], 2) . ' sq ft</td></tr>';
        echo '</table><br><br>';

        echo '<h3>Detailed Building Data</h3>';
        echo '<table border="1" cellpadding="5" cellspacing="0">';

        echo '<tr>';
        foreach ($data[0] as $header) {
            echo '<th>' . htmlspecialchars($header) . '</th>';
        }
        echo '</tr>';

        for ($i = 1; $i < count($data); $i++) {
            $row = $data[$i];
            $rowClass = '';
            if ($row[10] === 'VARIATION' || $row[12] === 'VARIATION') {
                $rowClass = 'class="variation-mismatch"';
            } elseif ($row[10] === 'MATCH' && $row[12] === 'MATCH') {
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
        echo '<tr><td style="background-color: #C6EFCE; border:1px solid #000;">&nbsp;&nbsp;&nbsp;&nbsp;</td><td><strong>Match:</strong> No variations found</td></tr>';
        echo '<tr><td style="background-color: #FFC7CE; border:1px solid #000;">&nbsp;&nbsp;&nbsp;&nbsp;</td><td><strong>Variation:</strong> Area or Usage mismatch detected</td></tr>';
        echo '</table></body></html>';
        exit;
    }

    public function mapView($ward_no)
    {
        $userId = Auth::user();
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

        // Point Data
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

        // Polygon Data
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

        // Shop Data
        $shopDatas = DB::table($shopTableName)->get();

        // Group shops by point_data_id
        $shopsGrouped = $shopDatas->groupBy('point_data_id');

        // Attach shops into pointdata
        foreach ($pointDatas as $pointdata) {

            $pointdata->shops = $shopsGrouped[$pointdata->id] ?? collect();
        }

        // Group pointdata by point_gisid
        $pointGrouped = collect($pointDatas)->groupBy('point_gisid');

        // Attach pointdata into polygondata
        foreach ($polygonDatas as $polygondata) {

            $polygondata->pointdata = $pointGrouped[$polygondata->gisid] ?? collect();

            // Optional statistics
            $polygondata->total_points = count($polygondata->pointdata);

            $polygondata->total_shops = collect($polygondata->pointdata)
                ->sum(function ($point) {
                    return count($point->shops);
                });
        }



        // Get polygons (buildings) - only needed fields
        $polygons = Schema::hasTable($polygonsTableName)
            ? DB::table($polygonsTableName)->select('gisid', 'coordinates', 'sqfeet')->get()
            : [];

        // Get lines (roads) - only needed fields
        $lines = Schema::hasTable($linesTableName)
            ? DB::table($linesTableName)->select('gisid', 'coordinates')->get()
            : [];


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

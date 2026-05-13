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
        $user = Auth::guard('corporation')->user();

        if (!$user) {
            return redirect()->route('corporation.login');
        }

        $corporation = Corporation::find($user->corporation_id);

        if (!$corporation) {
            return back()->with('error', 'Corporation not found');
        }

        // Get wards
        $wards = Ward::where('corporation_id', $corporation->id)
            ->where('status', 'active')
            ->get();

        $ward_count = $wards->count();

        $wards_per_zones = Ward::where('corporation_id', $corporation->id)
            ->where('status', 'active')
            ->select('zone', DB::raw('count(*) as total'))
            ->groupBy('zone')
            ->get();

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
        $zonesWithWards = [];
        $chartData = []; // For charts

        foreach ($wards_per_zones as $wards_per_zone) {
            $wardlists = Ward::where('zone', $wards_per_zone->zone)->get();

            $zoneData = [
                'zone' => $wards_per_zone->zone,
                'wards' => []
            ];

            foreach ($wardlists as $wardlist) {
                $pointdatatable = $this->getPointDataTable($corporation->id, $wardlist->ward_no, $wardlist->zone);
                $polygondatatable = $this->getPolygonDataTable($corporation->id, $wardlist->ward_no, $wardlist->zone);
                $polygontable = $this->getPolygonTable($corporation->id, $wardlist->ward_no, $wardlist->zone);
                $roadtable = $this->getLineTable($corporation->id, $wardlist->ward_no, $wardlist->zone);

                // Get counts
                $buildingCount = 0;
                if ($polygontable && Schema::hasTable($polygontable)) {
                    $buildingCount = DB::table($polygontable)->count();
                }

                $surveyedBuildingCount = 0;
                if ($polygondatatable && Schema::hasTable($polygondatatable)) {
                    $surveyedBuildingCount = DB::table($polygondatatable)->count();
                }

                $pointCount = 0;
                if ($pointdatatable && Schema::hasTable($pointdatatable)) {
                    $pointCount = DB::table($pointdatatable)->count();
                }

                $roadCount = 0;
                if ($roadtable && Schema::hasTable($roadtable)) {
                    $roadCount = DB::table($roadtable)->count();
                }

                $misCount = 0;
                $misWardTable = "mis_corporation_{$corporation->id}";
                if (Schema::hasTable($misWardTable)) {
                    $misCount = DB::table($misWardTable)
                        ->where('ward_no', $wardlist->ward_no)
                        ->count();
                }

                // Calculate variations for this ward
                $variationStats = $this->calculateWardVariations($corporation->id, $wardlist->zone, $wardlist->ward_no);

                // Accumulate totals
                $total_buildings += $buildingCount;
                $total_area_variation += $variationStats['area_variation_count'];
                $total_usage_variation += $variationStats['usage_variation_count'];

                // Prepare chart data - FIXED structure
                $chartData[] = [
                    'ward' => "Ward {$wardlist->ward_no}",
                    'ward_no' => $wardlist->ward_no,
                    'area_variation' => $variationStats['area_variation_count'],
                    'usage_variation' => $variationStats['usage_variation_count'],
                    'total_buildings' => $buildingCount,
                    'areaVariationCount' => $variationStats['area_variation_count'],
                    'usageVariationCount' => $variationStats['usage_variation_count']
                ];

                $data = [
                    "zone"                     => $wardlist->zone,
                    "ward_no"                  => $wardlist->ward_no,
                    "pointdatatable"           => $pointdatatable,
                    "polygondatatable"         => $polygondatatable,
                    "polygontable"             => $polygontable,
                    "roadtable"                => $roadtable,
                    "buildingCount"            => $buildingCount,
                    "surveyedBuildingCount"    => $surveyedBuildingCount,
                    "pointCount"               => $pointCount,
                    "roadCount"                => $roadCount,
                    "misCount"                 => $misCount,
                    "areaVariationCount"       => $variationStats['area_variation_count'],
                    "usageVariationCount"      => $variationStats['usage_variation_count'],
                    "areaVariationPercentage"  => $variationStats['area_variation_percentage'],
                    "usageVariationPercentage" => $variationStats['usage_variation_percentage'],
                ];

                $collections[] = $data;

                $zoneData['wards'][] = [
                    'ward_no' => $wardlist->ward_no,
                    'buildingCount' => $buildingCount,
                    'surveyedCount' => $surveyedBuildingCount,
                    'pointCount' => $pointCount,
                    'roadCount' => $roadCount,
                    'misCount' => $misCount,
                    'areaVariationCount' => $variationStats['area_variation_count'],
                    'usageVariationCount' => $variationStats['usage_variation_count'],
                ];
            }

            $zonesWithWards[] = $zoneData;
        }

        return view('corporation.dashboard', [
            "corporation" => $corporation,
            "ward_count"  => $ward_count,
            "mis_count"   => $mis_count,
            "collections" => $collections,
            "zonesWithWards" => $zonesWithWards,
            "total_buildings" => $total_buildings,
            "total_area_variation" => $total_area_variation,
            "total_usage_variation" => $total_usage_variation,
            "area_variation_percentage" => $total_buildings > 0 ? round(($total_area_variation / $total_buildings) * 100, 1) : 0,
            "usage_variation_percentage" => $total_buildings > 0 ? round(($total_usage_variation / $total_buildings) * 100, 1) : 0,
            "chartData" => $chartData // Pass as array, not JSON
        ]);
    }

    private function calculateWardVariations($corporationId, $zone, $wardNo)
    {
        $zone = strtolower(trim($zone));
        $wardNo = (int)$wardNo;

        $polygonsTableName = "polygon_{$corporationId}_{$zone}_{$wardNo}";
        $polygonDataTableName = "polygondata_{$corporationId}_{$zone}_{$wardNo}";
        $pointDataTableName = "pointdata_{$corporationId}_{$zone}_{$wardNo}";
        $misTableName = "mis_corporation_{$corporationId}";

        if (!Schema::hasTable($polygonsTableName) || !Schema::hasTable($pointDataTableName)) {
            return [
                'area_variation_count' => 0,
                'usage_variation_count' => 0,
                'area_variation_percentage' => 0,
                'usage_variation_percentage' => 0
            ];
        }

        // Get polygon data
        $polygons = DB::table($polygonsTableName)->get();
        $polygonDatas = Schema::hasTable($polygonDataTableName) ?
            DB::table($polygonDataTableName)->get()->keyBy('gisid') : collect();

        // Get point data - using bill_usage column
        $pointDataQuery = DB::table($pointDataTableName . ' as pd')
            ->leftJoin($misTableName . ' as mis', 'pd.assessment', '=', 'mis.assessment')
            ->select(
                'pd.point_gisid',
                'pd.assessment',
                'pd.qcsqfeet',
                'pd.bill_usage',
                'mis.plot_area'
            );

        $pointDatas = $pointDataQuery->get();

        // Group point data by point_gisid
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

        foreach ($polygons as $polygon) {
            $gisid = $polygon->gisid;
            $polygonSqfeet = floatval($polygon->sqfeet ?? 0);

            // Get polygon data for floor info
            $polyData = $polygonDatas->get($gisid);

            // Calculate building area
            if ($polyData) {
                $numberFloor = floatval($polyData->number_floor ?? 0);
                $basement = floatval($polyData->basement ?? 0);
                $buildingArea = ($numberFloor + $basement) * $polygonSqfeet;
                $buildingUsage = $polyData->building_usage ?? null;
            } else {
                $buildingArea = $polygonSqfeet;
                $buildingUsage = null;
            }

            // Calculate assessment area from point data
            $assessmentArea = 0;
            $hasUsageMismatch = false;

            if (isset($pointDataByGisid[$gisid])) {
                foreach ($pointDataByGisid[$gisid] as $pointData) {
                    // Get area - try qcsqfeet first, then plot_area
                    $pointArea = 0;
                    if (isset($pointData->qcsqfeet) && $pointData->qcsqfeet > 0) {
                        $pointArea = floatval($pointData->qcsqfeet);
                    } elseif (isset($pointData->plot_area) && $pointData->plot_area > 0) {
                        $pointArea = floatval($pointData->plot_area);
                    }
                    $assessmentArea += $pointArea;

                    // Check usage mismatch using bill_usage
                    $pointUsage = $pointData->bill_usage ?? null;
                    if ($buildingUsage && $pointUsage && strtoupper(trim($buildingUsage)) != strtoupper(trim($pointUsage))) {
                        $hasUsageMismatch = true;
                    }
                }
            }

            // Count variations if we have both values
            if ($buildingArea > 0 && $assessmentArea > 0) {
                $validBuildingsCount++;

                $areaDiff = abs($buildingArea - $assessmentArea);
                // Consider variation if difference is more than 1 sq ft
                if ($areaDiff > 1) {
                    $areaVariationCount++;
                }

                if ($hasUsageMismatch) {
                    $usageVariationCount++;
                }
            }
        }

        return [
            'area_variation_count' => $areaVariationCount,
            'usage_variation_count' => $usageVariationCount,
            'area_variation_percentage' => $validBuildingsCount > 0 ? round(($areaVariationCount / $validBuildingsCount) * 100, 1) : 0,
            'usage_variation_percentage' => $validBuildingsCount > 0 ? round(($usageVariationCount / $validBuildingsCount) * 100, 1) : 0,
        ];
    }

    public function mapView($ward_no)
    {
        $userId = Auth::user();
        $warddetail = Ward::where('corporation_id', $userId->corporation_id)->where('ward_no', $ward_no)->first();

        if (!$warddetail) {
            return back()->with('error', 'Ward not found');
        }

        $zone = strtolower(trim($warddetail->zone));
        $wardNo = (int)$warddetail->ward_no;
        $corp = (int)$warddetail->corporation_id;

        // Table names
        $polygonsTableName = "polygon_{$corp}_{$zone}_{$wardNo}";
        $polygonDataTableName = "polygondata_{$corp}_{$zone}_{$wardNo}";
        $pointsTableName = "point_{$corp}_{$zone}_{$wardNo}";
        $pointDataTableName = "pointdata_{$corp}_{$zone}_{$wardNo}";
        $linesTableName = "line_{$corp}_{$zone}_{$wardNo}";
        $misTableName = "mis_corporation_{$corp}";
        $waterTaxTableName = "watertax_corporation_{$corp}";
        $shopsTableName = "shops_corporation_{$corp}";

        // Get data with error handling for missing tables
        $polygons = Schema::hasTable($polygonsTableName) ? DB::table($polygonsTableName)->get() : [];
        $lines = Schema::hasTable($linesTableName) ? DB::table($linesTableName)->get() : [];
        $points = Schema::hasTable($pointsTableName) ? DB::table($pointsTableName)->get() : [];
        $polygonDatas = Schema::hasTable($polygonDataTableName) ? DB::table($polygonDataTableName)->get() : [];
        $pointDatas = Schema::hasTable($pointDataTableName) ? DB::table($pointDataTableName)->get() : [];

        // Get shops data
        $shopDatas = Schema::hasTable($shopsTableName) ? DB::table($shopsTableName)->get() : [];

        // Get MIS data
        $misData = [];
        if (Schema::hasTable($misTableName)) {
            $query = DB::table($misTableName . ' as mis');

            if (Schema::hasTable($waterTaxTableName)) {
                $waterTaxColumns = Schema::getColumnListing($waterTaxTableName);
                $selectColumns = ['mis.*'];

                if (in_array('watertax_no', $waterTaxColumns)) {
                    $selectColumns[] = 'wt.watertax_no';
                }
                if (in_array('old_watertax_no', $waterTaxColumns)) {
                    $selectColumns[] = 'wt.old_watertax_no';
                }
                if (in_array('water_tax', $waterTaxColumns)) {
                    $selectColumns[] = 'wt.water_tax as water_tax_amount';
                }
                if (in_array('water_tax_amount', $waterTaxColumns)) {
                    $selectColumns[] = 'wt.water_tax_amount';
                }

                $misData = $query->leftJoin($waterTaxTableName . ' as wt', 'mis.assessment', '=', 'wt.assessment')
                    ->select($selectColumns)
                    ->get();
            } else {
                $misData = $query->get();
            }
        }

        // Get unique road names from misData
        $uniqueRoadNames = [];
        if (Schema::hasTable($misTableName)) {
            $uniqueRoadNames = DB::table($misTableName)
                ->select('road_name')
                ->whereNotNull('road_name')
                ->where('road_name', '!=', '')
                ->distinct()
                ->orderBy('road_name')
                ->pluck('road_name');
        }

        $ward = $warddetail;

        // Add table names and ids to point data for reference
        foreach ($pointDatas as $pointData) {
            $pointData->table_name = $pointDataTableName;
        }

        return view('corporation.ward-map', compact(
            'ward',
            'polygons',
            'points',
            'lines',
            'polygonDatas',
            'pointDatas',
            'misData',
            'shopDatas',
            'uniqueRoadNames'
        ));
    }

    /**
     * Update assessment data by ID
     */
    public function updateAssessment(Request $request)
    {
        try {
            $assessmentId = $request->id;
            $assessmentNo = $request->assessment_no;
            $squareFeet = $request->square_feet;
            $usage = $request->usage;

            $user = Auth::user();
            $corpId = $user->corporation_id;

            $pointDataTable = $request->point_data_table;

            if (!$pointDataTable || !Schema::hasTable($pointDataTable)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Table not found: ' . $pointDataTable
                ]);
            }

            // Check if columns exist
            $columns = Schema::getColumnListing($pointDataTable);
            $updateData = [];

            // Update using actual column names from your table: qcsqfeet and qcusage
            if (in_array('qcsqfeet', $columns)) {
                $updateData['qcsqfeet'] = $squareFeet;
            } else if (in_array('sqfeet', $columns)) {
                $updateData['sqfeet'] = $squareFeet;
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Column "qcsqfeet" or "sqfeet" not found in table'
                ]);
            }

            if (in_array('qcusage', $columns)) {
                $updateData['qcusage'] = $usage;
            } else if (in_array('usage', $columns)) {
                $updateData['usage'] = $usage;
            } else if (in_array('bill_usage', $columns)) {
                $updateData['bill_usage'] = $usage;
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Column "qcusage", "usage", or "bill_usage" not found in table'
                ]);
            }

            if (in_array('updated_at', $columns)) {
                $updateData['updated_at'] = now();
            }

            // Log the update data for debugging
            \Log::info('Updating assessment with data:', [
                'table' => $pointDataTable,
                'id' => $assessmentId,
                'assessment_no' => $assessmentNo,
                'update_data' => $updateData
            ]);

            // Update by ID if provided, otherwise by assessment number
            if ($assessmentId && !empty($assessmentId)) {
                $updated = DB::table($pointDataTable)
                    ->where('id', $assessmentId)
                    ->update($updateData);

                \Log::info('Update by ID result:', ['updated' => $updated, 'id' => $assessmentId]);
            } else if ($assessmentNo && !empty($assessmentNo)) {
                $updated = DB::table($pointDataTable)
                    ->where('assessment', $assessmentNo)
                    ->update($updateData);

                \Log::info('Update by Assessment No result:', ['updated' => $updated, 'assessment_no' => $assessmentNo]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No ID or Assessment Number provided'
                ]);
            }

            if ($updated) {
                // Fetch the updated record to confirm
                if ($assessmentId) {
                    $updatedRecord = DB::table($pointDataTable)->where('id', $assessmentId)->first();
                } else {
                    $updatedRecord = DB::table($pointDataTable)->where('assessment', $assessmentNo)->first();
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Assessment updated successfully',
                    'data' => [
                        'id' => $updatedRecord->id ?? null,
                        'assessment' => $updatedRecord->assessment ?? null,
                        'qcsqfeet' => $updatedRecord->qcsqfeet ?? null,
                        'qcusage' => $updatedRecord->qcusage ?? null,
                        'sqfeet' => $updatedRecord->sqfeet ?? null,
                        'usage' => $updatedRecord->usage ?? null,
                        'bill_usage' => $updatedRecord->bill_usage ?? null
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Assessment not found or no changes made. Please check if the ID/Assessment Number exists.'
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Error updating assessment:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get assessment details by assessment number or ID
     */
    public function getAssessmentDetails(Request $request)
    {
        try {
            $assessmentNo = $request->assessment_no;
            $assessmentId = $request->assessment_id;
            $user = Auth::user();
            $corpId = $user->corporation_id;

            $pointDataTable = $request->point_data_table;

            if (!$pointDataTable || !Schema::hasTable($pointDataTable)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Table not found'
                ]);
            }

            // Query by ID if provided, otherwise by assessment number
            $query = DB::table($pointDataTable);
            if ($assessmentId && !empty($assessmentId)) {
                $query->where('id', $assessmentId);
            } else if ($assessmentNo && !empty($assessmentNo)) {
                $query->where('assessment', $assessmentNo);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No ID or Assessment Number provided'
                ]);
            }

            $data = $query->first();

            if ($data) {
                // Map the response to include sqfeet and usage for the frontend
                $responseData = (array)$data;
                $responseData['sqfeet'] = $data->qcsqfeet ?? $data->sqfeet ?? null;
                $responseData['usage'] = $data->qcusage ?? $data->usage ?? $data->bill_usage ?? null;

                return response()->json([
                    'success' => true,
                    'data' => $responseData
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Assessment not found'
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Error getting assessment details:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Add missing columns to point data table if needed
     */
    public function addMissingColumns(Request $request)
    {
        try {
            $tableName = $request->table_name;

            if (!Schema::hasTable($tableName)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Table not found'
                ]);
            }

            $columns = Schema::getColumnListing($tableName);
            $addedColumns = [];

            if (!in_array('sqfeet', $columns)) {
                Schema::table($tableName, function ($table) {
                    $table->decimal('sqfeet', 10, 2)->nullable();
                });
                $addedColumns[] = 'sqfeet';
            }

            if (!in_array('usage', $columns)) {
                Schema::table($tableName, function ($table) {
                    $table->string('usage', 50)->nullable();
                });
                $addedColumns[] = 'usage';
            }

            if (!in_array('updated_at', $columns)) {
                Schema::table($tableName, function ($table) {
                    $table->timestamp('updated_at')->nullable();
                });
                $addedColumns[] = 'updated_at';
            }

            if (count($addedColumns) > 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'Added columns: ' . implode(', ', $addedColumns)
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'message' => 'All required columns already exist'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
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

    public function filterWardData(Request $request)
    {
        try {
            $wardId = $request->ward_id;
            $areaFilter = $request->area_filter;
            $areaMin = $request->area_min ? (float)$request->area_min : null;
            $areaMax = $request->area_max ? (float)$request->area_max : null;
            $usageFilter = $request->usage_filter;
            $buildingUsage = $request->building_usage;

            // Get ward data
            $ward = Ward::find($wardId);
            if (!$ward) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ward not found'
                ], 404);
            }

            $corporationId = $ward->corporation_id;
            $zone = strtolower(trim($ward->zone));
            $wardNo = (int)$ward->ward_no;

            // Table names
            $polygonsTableName = "polygon_{$corporationId}_{$zone}_{$wardNo}";
            $polygonDataTableName = "polygondata_{$corporationId}_{$zone}_{$wardNo}";
            $pointsTableName = "point_{$corporationId}_{$zone}_{$wardNo}";
            $pointDataTableName = "pointdata_{$corporationId}_{$zone}_{$wardNo}";
            $linesTableName = "line_{$corporationId}_{$zone}_{$wardNo}";
            $shopsTableName = "shopdata_{$corporationId}_{$zone}_{$wardNo}";
            $misTableName = "mis_corporation_{$corporationId}";

            // Get all data first
            $allPolygons = Schema::hasTable($polygonsTableName) ? DB::table($polygonsTableName)->get() : collect();
            $allPolygonDatas = Schema::hasTable($polygonDataTableName) ? DB::table($polygonDataTableName)->get() : collect();
            $allPoints = Schema::hasTable($pointsTableName) ? DB::table($pointsTableName)->get() : collect();
            $allPointDatas = Schema::hasTable($pointDataTableName) ? DB::table($pointDataTableName)->get() : collect();
            $allLines = Schema::hasTable($linesTableName) ? DB::table($linesTableName)->get() : collect();
            $allShops = Schema::hasTable($shopsTableName) ? DB::table($shopsTableName)->get() : collect();

            // If no filters applied, return all data
            if ($areaFilter === 'all' && $usageFilter === 'all') {
                return response()->json([
                    'success' => true,
                    'polygons' => $allPolygons,
                    'lines' => $allLines,
                    'points' => $allPoints,
                    'pointDatas' => $allPointDatas,
                    'polygonDatas' => $allPolygonDatas,
                    'shopDatas' => $allShops,
                    'count' => $allPolygons->count()
                ]);
            }

            // Pre-load all MIS data for assessments
            $allAssessments = [];
            foreach ($allPointDatas as $pointData) {
                if (!empty($pointData->assessment)) {
                    $allAssessments[] = $pointData->assessment;
                }
            }

            // Remove duplicates
            $allAssessments = array_unique($allAssessments);

            // Load plot areas from MIS table if it exists
            $misPlotAreas = collect();
            if (Schema::hasTable($misTableName) && !empty($allAssessments)) {
                $misPlotAreas = DB::table($misTableName)
                    ->select('assessment', 'plot_area')
                    ->whereIn('assessment', $allAssessments)
                    ->get()
                    ->keyBy('assessment');
            }

            // Create maps for quick lookups
            $polygonDataMap = [];
            foreach ($allPolygonDatas as $polygonData) {
                $polygonDataMap[$polygonData->gisid] = $polygonData;
            }

            // Group point data by point_gisid
            $pointDataByGisid = [];
            foreach ($allPointDatas as $pointData) {
                if (!isset($pointDataByGisid[$pointData->point_gisid])) {
                    $pointDataByGisid[$pointData->point_gisid] = [];
                }
                $pointDataByGisid[$pointData->point_gisid][] = $pointData;
            }

            // Calculate variations for each polygon
            $filteredPolygons = [];
            $filteredGisids = [];

            foreach ($allPolygons as $polygon) {
                $gisid = $polygon->gisid;
                $totalSqFeet = 0;
                $buildingUsageValue = null;
                $assessmentTotal = 0;
                $usageVariation = false;
                $areaVariationValue = 0;

                // Get polygon data if exists
                if (isset($polygonDataMap[$gisid])) {
                    $polygonData = $polygonDataMap[$gisid];

                    // Get building usage
                    $buildingUsageValue = $polygonData->building_usage ?? null;

                    // Calculate total building area
                    $sqfeet = floatval($polygon->sqfeet ?? 0);
                    $totalFloor = floatval($polygonData->total_floor ?? $polygonData->number_floor ?? 0);
                    $floorPercentage = floatval($polygonData->percentage ?? 100);
                    $basement = floatval($polygonData->basement ?? 0);
                    $totalSqFeet = $sqfeet * ($totalFloor + ($floorPercentage / 100) + $basement);
                } else {
                    $totalSqFeet = floatval($polygon->sqfeet ?? 0);
                }

                // Calculate assessment total area from point data
                if (isset($pointDataByGisid[$gisid])) {
                    foreach ($pointDataByGisid[$gisid] as $assessment) {
                        // Get plot_area from MIS table using assessment value
                        $plotAreaFromMis = 0;
                        if ($misPlotAreas->has($assessment->assessment)) {
                            $plotAreaFromMis = floatval($misPlotAreas[$assessment->assessment]->plot_area);
                        }

                        // Use plot_area from pointdata or from MIS table
                        $plotArea = floatval($assessment->plot_area ?? $plotAreaFromMis);

                        // Use QC values if available
                        $qcSqft = floatval($assessment->qcsqfeet ?? 0);
                        if ($qcSqft > 0) {
                            $assessmentTotal += $qcSqft;
                        } else {
                            $assessmentTotal += $plotArea;
                        }

                        // Check for usage variation
                        $assessmentUsage = $assessment->qcusage ?? $assessment->usage ?? $assessment->bill_usage ?? null;
                        if ($buildingUsageValue && $assessmentUsage && strtoupper($buildingUsageValue) != strtoupper($assessmentUsage)) {
                            $usageVariation = true;
                        }
                    }
                }

                // Calculate area variation (absolute difference)
                $areaVariationValue = abs($totalSqFeet - $assessmentTotal);

                // Store variation data for filtering
                $polygon->calculated_total_sqfeet = $totalSqFeet;
                $polygon->assessment_total = $assessmentTotal;
                $polygon->area_variation = $areaVariationValue;
                $polygon->has_usage_variation = $usageVariation;
                $polygon->building_usage_value = $buildingUsageValue;

                // Apply filters
                $passFilter = true;

                // Area filter
                if ($areaFilter === 'variation') {
                    // Only include buildings with area variation > 0
                    if ($areaVariationValue <= 0) {
                        $passFilter = false;
                    }
                } elseif ($areaFilter === 'range') {
                    // Only include buildings with area variation within range
                    if ($areaMin !== null && $areaMax !== null) {
                        if ($areaVariationValue < $areaMin || $areaVariationValue > $areaMax) {
                            $passFilter = false;
                        }
                    } elseif ($areaMin !== null) {
                        if ($areaVariationValue < $areaMin) {
                            $passFilter = false;
                        }
                    } elseif ($areaMax !== null) {
                        if ($areaVariationValue > $areaMax) {
                            $passFilter = false;
                        }
                    }
                }

                // Usage filter
                if ($passFilter && $usageFilter === 'variation') {
                    // Only include buildings with usage variation
                    if (!$usageVariation) {
                        $passFilter = false;
                    }
                } elseif ($passFilter && $usageFilter === 'specific') {
                    // Only include buildings with specific usage
                    if ($buildingUsage && $buildingUsageValue) {
                        if (strtoupper($buildingUsageValue) != strtoupper($buildingUsage)) {
                            $passFilter = false;
                        }
                    } elseif ($buildingUsage && !$buildingUsageValue) {
                        $passFilter = false;
                    }
                }

                if ($passFilter) {
                    $filteredPolygons[] = $polygon;
                    $filteredGisids[] = $gisid;
                }
            }

            // Filter related data based on filtered gisids
            $filteredPoints = $allPoints->filter(function ($point) use ($filteredGisids) {
                return in_array($point->gisid, $filteredGisids);
            })->values();

            $filteredPointDatas = $allPointDatas->filter(function ($pointData) use ($filteredGisids) {
                return in_array($pointData->point_gisid, $filteredGisids);
            })->values();

            $filteredPolygonDatas = $allPolygonDatas->filter(function ($polygonData) use ($filteredGisids) {
                return in_array($polygonData->gisid, $filteredGisids);
            })->values();

            $filteredShops = $allShops->filter(function ($shop) use ($filteredPointDatas) {
                $pointDataIds = $filteredPointDatas->pluck('id')->toArray();
                return in_array($shop->point_data_id, $pointDataIds);
            })->values();

            // Add table name to point datas for reference
            foreach ($filteredPointDatas as $pointData) {
                $pointData->table_name = $pointDataTableName;
            }

            return response()->json([
                'success' => true,
                'polygons' => $filteredPolygons,
                'lines' => $allLines,
                'points' => $filteredPoints,
                'pointDatas' => $filteredPointDatas,
                'polygonDatas' => $filteredPolygonDatas,
                'shopDatas' => $filteredShops,
                'count' => count($filteredPolygons)
            ]);
        } catch (\Exception $e) {
            \Log::error('Filter error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function resetWardData(Request $request)
    {
        try {
            $wardId = $request->ward_id;

            $ward = Ward::find($wardId);
            if (!$ward) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ward not found'
                ], 404);
            }

            $corporationId = $ward->corporation_id;
            $zone = strtolower(trim($ward->zone));
            $wardNo = (int)$ward->ward_no;

            // Table names
            $polygonsTableName = "polygon_{$corporationId}_{$zone}_{$wardNo}";
            $polygonDataTableName = "polygondata_{$corporationId}_{$zone}_{$wardNo}";
            $pointsTableName = "point_{$corporationId}_{$zone}_{$wardNo}";
            $pointDataTableName = "pointdata_{$corporationId}_{$zone}_{$wardNo}";
            $linesTableName = "line_{$corporationId}_{$zone}_{$wardNo}";
            $shopsTableName = "shops_corporation_{$corporationId}";

            // Get all data for this ward
            $polygons = Schema::hasTable($polygonsTableName) ? DB::table($polygonsTableName)->get() : [];
            $lines = Schema::hasTable($linesTableName) ? DB::table($linesTableName)->get() : [];
            $points = Schema::hasTable($pointsTableName) ? DB::table($pointsTableName)->get() : [];
            $pointDatas = Schema::hasTable($pointDataTableName) ? DB::table($pointDataTableName)->get() : [];
            $polygonDatas = Schema::hasTable($polygonDataTableName) ? DB::table($polygonDataTableName)->get() : [];
            $shops = Schema::hasTable($shopsTableName) ? DB::table($shopsTableName)->get() : [];

            // Add table name to point datas for reference
            foreach ($pointDatas as $pointData) {
                $pointData->table_name = $pointDataTableName;
            }

            return response()->json([
                'success' => true,
                'polygons' => $polygons,
                'lines' => $lines,
                'points' => $points,
                'pointDatas' => $pointDatas,
                'polygonDatas' => $polygonDatas,
                'shopDatas' => $shops,
                'count' => count($polygons)
            ]);
        } catch (\Exception $e) {
            \Log::error('Reset error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
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

    // Table names
    $polygonsTableName = "polygon_{$corp}_{$zone}_{$wardNo}";
    $polygonDataTableName = "polygondata_{$corp}_{$zone}_{$wardNo}";
    $pointDataTableName = "pointdata_{$corp}_{$zone}_{$wardNo}";
    $misTableName = "mis_corporation_{$corp}";

    if (!Schema::hasTable($polygonsTableName)) {
        return back()->with('error', 'Building data not found for this ward');
    }

    // Get all polygons (buildings)
    $polygons = DB::table($polygonsTableName)->get();
    $polygonDatas = Schema::hasTable($polygonDataTableName)
        ? DB::table($polygonDataTableName)->get()->keyBy('gisid')
        : collect();

    // Get all point data (assessments/bills)
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
                'pd.bill_usage as point_usage',
                'pd.qcusage',
                'mis.plot_area',
                'mis.owner_name',
                'mis.address',
                'mis.road_name'
            );

        $pointDatas = $pointDataQuery->get();

        // Group by point_gisid
        foreach ($pointDatas as $pointData) {
            $gisid = $pointData->point_gisid;
            if (!isset($pointDataByGisid[$gisid])) {
                $pointDataByGisid[$gisid] = [];
            }
            $pointDataByGisid[$gisid][] = $pointData;
        }
    }

    // Prepare Excel data
    $excelData = [];
    $rowNumber = 1;

    // Headers
    $excelData[] = [
        'S.No',
        'GIS ID',
        'Building Sq Feet',
        'Number of Floors',
        'Basement',
        'Floor Percentage',
        'Total Building Area',
        'Building Usage',
        'Total Assessment Area (Sum of Bills)',
        'Area Variation',
        'Area Variation Status',
        'Number of Bills/Assessments',
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

        // Get polygon data (floor details)
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

            // Calculate total building area with floors and basement
            $totalBuildingArea = $polygonSqfeet * ($numberFloor + ($floorPercentage / 100) + $basement);
        }

        // Get assessment data for this building
        $assessmentArea = 0;
        $assessmentCount = 0;
        $hasUsageMismatch = false;
        $ownerName = '';
        $address = '';
        $roadName = '';
        $billUsages = [];

        if (isset($pointDataByGisid[$gisid])) {
            $assessmentCount = count($pointDataByGisid[$gisid]);

            foreach ($pointDataByGisid[$gisid] as $pointData) {
                // Calculate area
                $pointArea = 0;
                if (isset($pointData->qcsqfeet) && $pointData->qcsqfeet > 0) {
                    $pointArea = floatval($pointData->qcsqfeet);
                } elseif (isset($pointData->plot_area) && $pointData->plot_area > 0) {
                    $pointArea = floatval($pointData->plot_area);
                }
                $assessmentArea += $pointArea;

                // Check usage mismatch
                $pointUsage = $pointData->bill_usage ?? $pointData->qcusage ?? $pointData->point_usage ?? null;
                if ($buildingUsage && $pointUsage && strtoupper(trim($buildingUsage)) != strtoupper(trim($pointUsage))) {
                    $hasUsageMismatch = true;
                }

                // Get MIS details (use first assessment's data for reference)
                if (empty($ownerName) && isset($pointData->owner_name)) {
                    $ownerName = $pointData->owner_name;
                    $address = $pointData->address ?? '';
                    $roadName = $pointData->road_name ?? '';
                }

                $billUsages[] = $pointUsage;
            }
        }

        // Calculate variations
        $areaVariation = $totalBuildingArea - $assessmentArea;
        $areaVariationAbs = abs($areaVariation);
        $hasAreaVariation = $areaVariationAbs > 1;
        $isNegativeVariation = $areaVariation < 0;
        $variationPercentage = $totalBuildingArea > 0
            ? round(($areaVariationAbs / $totalBuildingArea) * 100, 2)
            : 0;

        // Determine status
        $areaStatus = $hasAreaVariation ? 'VARIATION' : 'MATCH';
        $usageStatus = $hasUsageMismatch ? 'VARIATION' : 'MATCH';
        $negativeStatus = $isNegativeVariation ? 'YES' : 'NO';

        // Add to Excel data
        $excelData[] = [
            'S.No' => $rowNumber++,
            'GIS ID' => $gisid,
            'Building Sq Feet' => $polygonSqfeet,
            'Number of Floors' => $numberFloor,
            'Basement' => $basement,
            'Floor Percentage' => $floorPercentage . '%',
            'Total Building Area' => round($totalBuildingArea, 2),
            'Building Usage' => $buildingUsage ?? 'N/A',
            'Total Assessment Area (Sum of Bills)' => round($assessmentArea, 2),
            'Area Variation' => round($areaVariation, 2),
            'Area Variation Status' => $areaStatus,
            'Number of Bills/Assessments' => $assessmentCount,
            'Usage Variation Status' => $usageStatus,
            'Negative Area Variation' => $negativeStatus,
            'Variation Percentage' => $variationPercentage . '%',
            'Owner Name' => $ownerName,
            'Address' => $address,
            'Road Name' => $roadName
        ];
    }

    // Add summary sheet data
    $totalBuildings = count($polygons);
    $buildingsWithAreaVariation = 0;
    $buildingsWithUsageVariation = 0;
    $buildingsWithNegativeVariation = 0;
    $totalBuildingAreaSum = 0;
    $totalAssessmentAreaSum = 0;

    foreach ($excelData as $index => $row) {
        if ($index === 0) continue; // Skip header

        if ($row['Area Variation Status'] === 'VARIATION') {
            $buildingsWithAreaVariation++;
        }
        if ($row['Usage Variation Status'] === 'VARIATION') {
            $buildingsWithUsageVariation++;
        }
        if ($row['Negative Area Variation'] === 'YES') {
            $buildingsWithNegativeVariation++;
        }
        $totalBuildingAreaSum += floatval($row['Total Building Area']);
        $totalAssessmentAreaSum += floatval($row['Total Assessment Area (Sum of Bills)']);
    }

    // Create Excel file using Laravel Excel or raw PHP
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

/**
 * Generate Excel file using PHP native functions
 */
private function generateExcel($data, $ward, $summary)
{
    // Create temporary file
    $filename = "ward_{$ward->ward_no}_building_variations_" . date('Ymd_His') . ".xls";

    // Set headers for Excel download
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    // Create HTML table for Excel
    echo '<html>';
    echo '<head>';
    echo '<meta charset="UTF-8">';
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
    echo '</style>';
    echo '</head>';
    echo '<body>';

    // Report Header
    echo '<div class="header">';
    echo '<h2>' . htmlspecialchars($summary['corporationName']) . '</h2>';
    echo '<h3>Building Variation Report - ' . htmlspecialchars($summary['wardName']) . ' (' . htmlspecialchars($summary['zone']) . ' Zone)</h3>';
    echo '</div>';

    echo '<div class="subheader">';
    echo 'Generated on: ' . date('d-m-Y H:i:s') . '<br>';
    echo '</div>';

    // Summary Statistics
    echo '<h3>Summary Statistics</h3>';
    echo '<table class="summary-table" border="1" cellpadding="5" cellspacing="0">';
    echo '<tr style="background-color: #E6E6E6;"><td width="50%"><strong>Total Buildings:</strong></td><td>' . $summary['totalBuildings'] . '</td></tr>';
    echo '<tr><td><strong>Buildings with Area Variation:</strong></td><td>' . $summary['areaVariationCount'] . ' (' . round(($summary['areaVariationCount'] / max(1, $summary['totalBuildings'])) * 100, 2) . '%)</td></tr>';
    echo '<tr style="background-color: #E6E6E6;"><td><strong>Buildings with Usage Variation:</strong></td><td>' . $summary['usageVariationCount'] . ' (' . round(($summary['usageVariationCount'] / max(1, $summary['totalBuildings'])) * 100, 2) . '%)</td></tr>';
    echo '<tr><td><strong>Buildings with Negative Variation (Assessment < Building):</strong></td><td>' . $summary['negativeVariationCount'] . ' (' . round(($summary['negativeVariationCount'] / max(1, $summary['totalBuildings'])) * 100, 2) . '%)</td></tr>';
    echo '<tr style="background-color: #E6E6E6;"><td><strong>Total Building Area (All Buildings):</strong></td><td>' . number_format($summary['totalBuildingArea'], 2) . ' sq ft</td></tr>';
    echo '<tr><td><strong>Total Assessment Area (All Bills):</strong></td><td>' . number_format($summary['totalAssessmentArea'], 2) . ' sq ft</td></tr>';
    echo '<tr style="background-color: #E6E6E6;"><td><strong>Total Area Variation:</strong></td><td>' . number_format($summary['totalBuildingArea'] - $summary['totalAssessmentArea'], 2) . ' sq ft</td></tr>';
    echo '</table>';

    echo '<br><br>';

    // Detailed Data Table
    echo '<h3>Detailed Building Data</h3>';
    echo '<table border="1" cellpadding="5" cellspacing="0">';

    // Headers
    echo '<tr>';
    foreach ($data[0] as $header) {
        echo '<th>' . htmlspecialchars($header) . '</th>';
    }
    echo '</tr>';

    // Data rows
    for ($i = 1; $i < count($data); $i++) {
        $row = $data[$i];
        $rowClass = '';

        // Color code rows based on variation status
        if ($row['Area Variation Status'] === 'VARIATION' || $row['Usage Variation Status'] === 'VARIATION') {
            $rowClass = 'class="variation-mismatch"';
        } elseif ($row['Area Variation Status'] === 'MATCH' && $row['Usage Variation Status'] === 'MATCH') {
            $rowClass = 'class="variation-match"';
        }

        echo '<tr ' . $rowClass . '>';
        foreach ($row as $key => $value) {
            echo '<td>' . htmlspecialchars($value) . '</td>';
        }
        echo '</tr>';
    }

    echo '</table>';

    // Legend
    echo '<br><br>';
    echo '<table border="0" cellpadding="5">';
    echo '<tr><td style="background-color: #C6EFCE; border:1px solid #000;">&nbsp;&nbsp;&nbsp;&nbsp;</td><td><strong>Match:</strong> No variations found</td></tr>';
    echo '<tr><td style="background-color: #FFC7CE; border:1px solid #000;">&nbsp;&nbsp;&nbsp;&nbsp;</td><td><strong>Variation:</strong> Area or Usage mismatch detected</td></tr>';
    echo '</table>';

    echo '</body>';
    echo '</html>';

    exit;
}
}

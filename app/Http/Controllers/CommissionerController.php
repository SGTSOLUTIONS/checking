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

        // Build collections
        $collections = [];
        $zonesWithWards = [];

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
                ];

                $collections[] = $data;

                $zoneData['wards'][] = [
                    'ward_no' => $wardlist->ward_no,
                    'buildingCount' => $buildingCount,
                    'surveyedCount' => $surveyedBuildingCount,
                    'pointCount' => $pointCount,
                    'roadCount' => $roadCount,
                    'misCount' => $misCount
                ];
            }

            $zonesWithWards[] = $zoneData;
        }

        return view('corporation.dashboard', [
            "corporation" => $corporation,
            "ward_count"  => $ward_count,
            "mis_count"   => $mis_count,
            "collections" => $collections,
            "zonesWithWards" => $zonesWithWards
        ]);
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
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Column "qcusage" or "usage" not found in table'
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
                        'usage' => $updatedRecord->usage ?? null
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
                $responseData['usage'] = $data->qcusage ?? $data->usage ?? null;

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
        return response()->json($request->all());
        try {
            $wardId = $request->ward_id;
            $areaFilter = $request->area_filter;
            $areaMin = (float)$request->area_min;
            $areaMax = (float)$request->area_max;
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

            // Get all data first
            $allPolygons = Schema::hasTable($polygonsTableName) ? DB::table($polygonsTableName)->get() : collect();
            $allPolygonDatas = Schema::hasTable($polygonDataTableName) ? DB::table($polygonDataTableName)->get() : collect();
            $allPoints = Schema::hasTable($pointsTableName) ? DB::table($pointsTableName)->get() : collect();
            $allPointDatas = Schema::hasTable($pointDataTableName) ? DB::table($pointDataTableName)->get() : collect();
            $allLines = Schema::hasTable($linesTableName) ? DB::table($linesTableName)->get() : collect();
            $allShops = Schema::hasTable($shopsTableName) ? DB::table($shopsTableName)->get() : collect();

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
            // return response()->json("data");
            if(u)

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

}

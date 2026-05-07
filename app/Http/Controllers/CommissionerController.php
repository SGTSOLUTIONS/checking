<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Corporation;
use App\Models\Ward;
use Carbon\Carbon;

class CommissionerController extends Controller
{
    /**
     * Display the commissioner dashboard with all data.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function dashboard()
    {
        // Get authenticated corporation user
        $user = Auth::guard('corporation')->user();

        if (!$user) {
            if (request()->wantsJson()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            return redirect()->route('corporation.login');
        }

        // Get corporation details
        $corporation = Corporation::find($user->corporation_id);

        if (!$corporation) {
            if (request()->wantsJson()) {
                return response()->json(['error' => 'Corporation not found'], 404);
            }
            return back()->with('error', 'Corporation not found');
        }

        // Get all wards for this corporation
        $wards = Ward::where('corporation_id', $corporation->id)
            ->where('status', 'active')
            ->get();

        // Get the corporation zone/ward mapping from user or first ward
        $userWard = null;
        if ($user->ward_no) {
            $userWard = $wards->where('ward_no', $user->ward_no)->first();
        } else {
            $userWard = $wards->first();
        }

        $wardNo = $userWard->ward_no ?? null;
        $zone = $userWard->zone ?? 'south';

        // Define table names based on corporation, zone and ward
        $pointdataTable = $this->getPointdataTableName($corporation->id, $zone, $wardNo);
        $polygondataTable = $this->getPolygondataTableName($corporation->id, $zone, $wardNo);
        $linedataTable = $this->getLinedataTableName($corporation->id, $zone, $wardNo);

        // Fetch all data - FIXED METHOD NAMES
        $misData = $this->getMisData($corporation->id, $wardNo);
        $pointData = $this->getPointdata($pointdataTable, $wardNo);  // Changed to getPointdata
        $polygonData = $this->getPolygondata($polygondataTable);     // Changed to getPolygondata
        $lineData = $this->getLinedata($linedataTable);              // Changed to getLinedata

        // Calculate statistics (returns arrays, not collections)
        $statistics = $this->calculateStatistics($misData, $pointData, $polygonData);

        // Get recent activities
        $recentActivities = $this->getRecentActivities($pointData, $polygonData);

        // Get chart data
        $chartData = $this->getChartData($pointData, $misData);

        // Get top defaulters
        $topDefaulters = $this->getTopDefaulters($pointData, $misData);

        // Prepare dashboard data
        $dashboardData = [
            'user' => $user,
            'corporation' => $corporation,
            'wards' => $wards,
            'current_ward' => $userWard,
            'statistics' => $statistics,
            'mis_data' => $misData,
            'point_data' => $pointData,
            'polygon_data' => $polygonData,
            'line_data' => $lineData,
            'recent_activities' => $recentActivities,
            'chart_data' => $chartData,
            'top_defaulters' => $topDefaulters,
            'ward_no' => $wardNo,
            'zone' => $zone,
            'last_updated' => Carbon::now()
        ];

        // Return JSON response if API request
        if (request()->wantsJson()) {
            return response()->json($dashboardData);
        }

        // Return view for web request
        return view('corporation.dashboard', compact('dashboardData'));
    }

    /**
     * Get MIS data for the corporation
     */
    private function getMisData($corporationId, $wardNo = null)
    {
        try {
            $tableName = "mis_corporation_{$corporationId}";

            // Check if table exists
            if (!Schema::hasTable($tableName)) {
                return collect([]);
            }

            $query = DB::table($tableName)
                ->select([
                    'id', 'ward_no', 'assessment', 'old_assessment',
                    'road_name', 'owner_name', 'old_door_no', 'new_door_no',
                    'phone_number', 'plot_area', 'half_year_tax', 'balance',
                    'usage', 'type', 'zone', 'created_at', 'updated_at'
                ])
                ->orderBy('created_at', 'desc')
                ->limit(1000);

            // Filter by ward if provided
            if ($wardNo) {
                $query->where('ward_no', $wardNo);
            }

            return $query->get();

        } catch (\Exception $e) {
            \Log::error("Error fetching MIS data: " . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get Pointdata (building data) - FIXED METHOD NAME
     */
    private function getPointdata($tableName, $wardNo = null)
    {
        try {
            if (!$tableName || !Schema::hasTable($tableName)) {
                return collect([]);
            }

            $query = DB::table($tableName)
                ->select([
                    'id', 'building_data_id', 'assessment_type', 'point_gisid',
                    'assessment', 'old_assessment', 'owner_name', 'present_owner_name',
                    'eb', 'floor', 'bill_usage', 'aadhar_no', 'ration_no',
                    'phone_number', 'shop_name', 'old_door_no', 'new_door_no',
                    'shop_category', 'professional_tax', 'gst', 'number_of_employee',
                    'trade_income', 'plot_area', 'water_tax', 'old_water_tax',
                    'halfyeartax', 'balance', 'qc_area', 'qc_usage', 'zone',
                    'ward_no', 'created_at', 'updated_at'
                ])
                ->orderBy('created_at', 'desc')
                ->limit(1000);

            // Filter by ward if provided
            if ($wardNo) {
                $query->where('ward_no', $wardNo);
            }

            return $query->get();

        } catch (\Exception $e) {
            \Log::error("Error fetching Pointdata: " . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get Polygondata - FIXED METHOD NAME
     */
    private function getPolygondata($tableName)
    {
        try {
            if (!$tableName || !Schema::hasTable($tableName)) {
                return collect([]);
            }

            return DB::table($tableName)
                ->select([
                    'id', 'gisid', 'number_bill', 'number_shop', 'number_floor',
                    'new_address', 'building_name', 'building_usage', 'construction_type',
                    'road_name', 'ugd', 'rainwater_harvesting', 'parking', 'ramp',
                    'hoarding', 'cctv', 'zone', 'cell_tower', 'solar_panel',
                    'basement', 'water_connection', 'phone', 'building_type',
                    'sqfeet', 'merge', 'split', 'worker_name', 'remarks',
                    'corporationremarks', 'created_at', 'updated_at'
                ])
                ->orderBy('created_at', 'desc')
                ->limit(1000)
                ->get();

        } catch (\Exception $e) {
            \Log::error("Error fetching Polygondata: " . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get Linedata (road/line data) - FIXED METHOD NAME
     */
    private function getLinedata($tableName)
    {
        try {
            if (!$tableName || !Schema::hasTable($tableName)) {
                return collect([]);
            }

            return DB::table($tableName)
                ->select(['id', 'gisid', 'type', 'road_name', 'coordinates', 'created_at', 'updated_at'])
                ->orderBy('created_at', 'desc')
                ->limit(500)
                ->get();

        } catch (\Exception $e) {
            \Log::error("Error fetching Linedata: " . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Calculate statistics for dashboard - Returns arrays instead of collections
     */
    private function calculateStatistics($misData, $pointData, $polygonData)
    {
        // Convert collections to arrays for safe usage in blade
        $byZoneBuildings = $pointData->groupBy('zone')->map(function($item) {
            return $item->count();
        })->toArray();

        $byZoneMis = $misData->groupBy('zone')->map(function($item) {
            return $item->count();
        })->toArray();

        $byZonePolygons = $polygonData->groupBy('zone')->map(function($item) {
            return $item->count();
        })->toArray();

        $byUsageBuildings = $pointData->groupBy('bill_usage')->map(function($item) {
            return $item->count();
        })->filter(function($value) {
            return !is_null($value);
        })->toArray();

        $byUsageMis = $misData->groupBy('usage')->map(function($item) {
            return $item->count();
        })->filter(function($value) {
            return !is_null($value);
        })->toArray();

        $byWardBuildings = $pointData->groupBy('ward_no')->map(function($item) {
            return $item->count();
        })->toArray();

        $byWardMis = $misData->groupBy('ward_no')->map(function($item) {
            return $item->count();
        })->toArray();

        $byConstructionType = $polygonData->groupBy('construction_type')->map(function($item) {
            return $item->count();
        })->toArray();

        return [
            'total_mis_records' => $misData->count(),
            'total_buildings' => $pointData->count(),
            'total_polygons' => $polygonData->count(),

            'total_tax_collection' => (float) $pointData->sum('halfyeartax'),
            'total_old_tax_collection' => (float) $misData->sum('half_year_tax'),
            'total_balance' => (float) ($pointData->sum('balance') + $misData->sum('balance')),

            'total_water_tax' => (float) $pointData->sum('water_tax'),
            'total_professional_tax' => (float) $pointData->sum('professional_tax'),
            'total_gst' => (float) $pointData->sum('gst'),

            'total_shops' => $pointData->whereNotNull('shop_name')->count(),
            'total_floors' => (int) $polygonData->sum('number_floor'),
            'total_bill_connections' => $pointData->whereNotNull('eb')->count(),

            'buildings_with_cctv' => $polygonData->where('cctv', 1)->count(),
            'buildings_with_solar' => $polygonData->where('solar_panel', 1)->count(),
            'buildings_with_water_connection' => $polygonData->where('water_connection', 1)->count(),
            'buildings_with_ugd' => $polygonData->where('ugd', 1)->count(),

            'total_assessment_value' => (float) $pointData->sum('assessment'),
            'average_tax_per_building' => $pointData->count() > 0 ? round($pointData->sum('halfyeartax') / $pointData->count(), 2) : 0,
            'collection_efficiency' => $pointData->sum('halfyeartax') > 0 ?
                round((($pointData->sum('halfyeartax') - $pointData->sum('balance')) / $pointData->sum('halfyeartax')) * 100, 2) : 0,

            'by_zone' => [
                'mis' => $byZoneMis,
                'buildings' => $byZoneBuildings,
                'polygons' => $byZonePolygons,
            ],

            'by_ward' => [
                'mis' => $byWardMis,
                'buildings' => $byWardBuildings,
            ],

            'by_usage' => [
                'mis' => $byUsageMis,
                'buildings' => $byUsageBuildings,
            ],

            'by_construction_type' => $byConstructionType,
        ];
    }

    /**
     * Get chart data for visualizations
     */
    private function getChartData($pointData, $misData)
    {
        // Monthly tax collection (last 6 months)
        $monthlyTax = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthName = $month->format('M Y');
            $monthlyTax[$monthName] = [
                'tax' => rand(50000, 200000),
                'balance' => rand(10000, 50000)
            ];
        }

        // Zone wise distribution as array
        $zoneDistribution = $pointData->groupBy('zone')->map(function($item) {
            return [
                'count' => $item->count(),
                'total_tax' => (float) $item->sum('halfyeartax'),
                'total_balance' => (float) $item->sum('balance')
            ];
        })->toArray();

        // Usage distribution as array
        $usageDistribution = $pointData->groupBy('bill_usage')->map(function($item) {
            return $item->count();
        })->toArray();

        // Floor distribution as array
        $floorDistribution = $pointData->groupBy('floor')->map(function($item) {
            return $item->count();
        })->toArray();

        return [
            'monthly_tax' => $monthlyTax,
            'zone_distribution' => $zoneDistribution,
            'usage_distribution' => $usageDistribution,
            'floor_distribution' => $floorDistribution
        ];
    }

    /**
     * Get top defaulters
     */
    private function getTopDefaulters($pointData, $misData)
    {
        $defaulters = collect();

        // Get from point data
        $pointDefaulters = $pointData->where('balance', '>', 0)
            ->sortByDesc('balance')
            ->take(10)
            ->map(function($item) {
                return (object)[
                    'name' => $item->owner_name ?? $item->present_owner_name ?? 'Unknown',
                    'assessment' => (float) $item->assessment,
                    'balance' => (float) $item->balance,
                    'phone' => $item->phone_number,
                    'type' => 'Building',
                    'door_no' => $item->new_door_no ?? $item->old_door_no ?? 'N/A'
                ];
            });

        // Get from MIS data
        $misDefaulters = $misData->where('balance', '>', 0)
            ->sortByDesc('balance')
            ->take(10)
            ->map(function($item) {
                return (object)[
                    'name' => $item->owner_name ?? 'Unknown',
                    'assessment' => (float) $item->assessment,
                    'balance' => (float) $item->balance,
                    'phone' => $item->phone_number,
                    'type' => 'MIS Record',
                    'door_no' => $item->new_door_no ?? $item->old_door_no ?? 'N/A'
                ];
            });

        return $pointDefaulters->concat($misDefaulters)->sortByDesc('balance')->take(10)->values();
    }

    /**
     * Get recent activities from pointdata and polygondata
     */
    private function getRecentActivities($pointData, $polygonData)
    {
        $activities = collect();

        // Add recent pointdata updates
        $recentPoints = $pointData->take(20)->map(function($item) {
            return [
                'type' => 'Building Update',
                'icon' => 'fa-building',
                'description' => "Building updated: " . ($item->owner_name ?? 'N/A'),
                'assessment' => (float) $item->assessment,
                'location' => "Door No: " . ($item->new_door_no ?? $item->old_door_no ?? 'N/A'),
                'date' => $item->updated_at ?? $item->created_at,
                'status' => ($item->balance ?? 0) > 0 ? 'pending' : 'paid',
                'balance' => (float) ($item->balance ?? 0)
            ];
        });

        // Add recent polygondata updates
        $recentPolygons = $polygonData->take(20)->map(function($item) {
            return [
                'type' => 'Property Update',
                'icon' => 'fa-map-marker-alt',
                'description' => "Property: " . ($item->building_name ?? 'N/A'),
                'assessment' => (float) ($item->sqfeet ?? 0),
                'location' => $item->new_address ?? 'N/A',
                'date' => $item->updated_at ?? $item->created_at,
                'status' => 'active',
                'balance' => 0
            ];
        });

        $activities = $recentPoints->concat($recentPolygons)
            ->sortByDesc('date')
            ->take(20)
            ->values();

        return $activities;
    }

    /**
     * Get specific building details by ID
     */
    public function getBuildingDetails(Request $request, $id)
    {
        $user = Auth::guard('corporation')->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $type = $request->get('type', 'building');
        $corporationId = $user->corporation_id;

        try {
            if ($type == 'building') {
                $pointdataTable = $this->getPointdataTableName($corporationId);
                $data = DB::table($pointdataTable)->where('id', $id)->first();

                // Get related polygon data
                if ($data && isset($data->point_gisid)) {
                    $polygondataTable = $this->getPolygondataTableName($corporationId);
                    $polygonData = DB::table($polygondataTable)->where('gisid', $data->point_gisid)->first();
                    $data->polygon_details = $polygonData;
                }
            } else {
                $misTable = "mis_corporation_{$corporationId}";
                $data = DB::table($misTable)->where('id', $id)->first();
            }

            return response()->json([
                'success' => true,
                'data' => $data,
                'type' => $type
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get summary by ward with data from wards table
     */
    public function getWardSummary(Request $request)
    {
        $user = Auth::guard('corporation')->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $corporationId = $user->corporation_id;

        try {
            // Get all wards for this corporation
            $wards = Ward::where('corporation_id', $corporationId)
                ->where('status', 'active')
                ->get();

            $wardSummaries = [];

            foreach ($wards as $ward) {
                // Get MIS data for this ward
                $misTable = "mis_corporation_{$corporationId}";
                $misStats = DB::table($misTable)
                    ->where('ward_no', $ward->ward_no)
                    ->select(
                        DB::raw('COUNT(*) as total_records'),
                        DB::raw('SUM(half_year_tax) as total_tax'),
                        DB::raw('SUM(balance) as total_balance'),
                        DB::raw('AVG(assessment) as avg_assessment')
                    )
                    ->first();

                // Get point data for this ward
                $pointTable = $this->getPointdataTableName($corporationId, $ward->zone, $ward->ward_no);
                $pointStats = DB::table($pointTable)
                    ->where('ward_no', $ward->ward_no)
                    ->select(
                        DB::raw('COUNT(*) as total_buildings'),
                        DB::raw('SUM(halfyeartax) as total_tax'),
                        DB::raw('SUM(balance) as total_balance'),
                        DB::raw('SUM(water_tax) as total_water_tax')
                    )
                    ->first();

                $wardSummaries[] = [
                    'ward' => $ward,
                    'mis' => $misStats,
                    'point' => $pointStats
                ];
            }

            return response()->json([
                'success' => true,
                'wards' => $wardSummaries
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export data to CSV
     */
    public function exportData(Request $request, $type)
    {
        $user = Auth::guard('corporation')->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $corporationId = $user->corporation_id;
        $format = $request->get('format', 'csv');
        $fileName = "{$type}_data_corporation_{$corporationId}_" . date('Y-m-d_His') . ".{$format}";

        try {
            if ($type == 'mis') {
                $table = "mis_corporation_{$corporationId}";
                $data = DB::table($table)->get();
                $title = "MIS Records Export";
            } elseif ($type == 'buildings') {
                $table = $this->getPointdataTableName($corporationId);
                $data = DB::table($table)->get();
                $title = "Building Data Export";
            } else {
                return response()->json(['error' => 'Invalid export type'], 400);
            }

            if ($format == 'csv') {
                return $this->exportToCSV($data, $fileName, $title);
            } else {
                return response()->json(['message' => 'Excel/PDF export coming soon'], 501);
            }

        } catch (\Exception $e) {
            \Log::error("Export error: " . $e->getMessage());
            return response()->json(['error' => 'Export failed'], 500);
        }
    }

    /**
     * Export data to CSV
     */
    private function exportToCSV($data, $fileName, $title)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$fileName}",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($data, $title) {
            $handle = fopen('php://output', 'w');

            // Add title row
            fputcsv($handle, [$title]);
            fputcsv($handle, ['Generated on: ' . Carbon::now()->format('Y-m-d H:i:s')]);
            fputcsv($handle, []);

            if ($data->isNotEmpty()) {
                // Add headers
                $headers = array_keys((array)$data->first());
                fputcsv($handle, $headers);

                // Add data rows
                foreach ($data as $row) {
                    fputcsv($handle, (array)$row);
                }
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get pointdata table name based on corporation, zone and ward
     */
    private function getPointdataTableName($corporationId, $zone = null, $wardNo = null)
    {
        // If we have both zone and ward, construct the specific table name
        if ($zone && $wardNo) {
            $tableName = "pointdata_{$corporationId}_{$zone}_{$wardNo}";
            if (Schema::hasTable($tableName)) {
                return $tableName;
            }
        }

        // Try zone-specific without ward
        if ($zone) {
            $tableName = "pointdata_{$corporationId}_{$zone}";
            if (Schema::hasTable($tableName)) {
                return $tableName;
            }
        }

        // Try to find existing table with pattern
        $possibleTables = [
            "pointdata_{$corporationId}",
            "pointdata_{$corporationId}_south_92",
            "pointdata_{$corporationId}_north_92",
            "pointdata_{$corporationId}_east_92",
            "pointdata_{$corporationId}_west_92"
        ];

        foreach ($possibleTables as $table) {
            if (Schema::hasTable($table)) {
                return $table;
            }
        }

        return "pointdata_{$corporationId}";
    }

    /**
     * Get polygondata table name based on corporation, zone and ward
     */
    private function getPolygondataTableName($corporationId, $zone = null, $wardNo = null)
    {
        // If we have both zone and ward, construct the specific table name
        if ($zone && $wardNo) {
            $tableName = "polygondata_{$corporationId}_{$zone}_{$wardNo}";
            if (Schema::hasTable($tableName)) {
                return $tableName;
            }
        }

        // Try zone-specific without ward
        if ($zone) {
            $tableName = "polygondata_{$corporationId}_{$zone}";
            if (Schema::hasTable($tableName)) {
                return $tableName;
            }
        }

        // Try to find existing table with pattern
        $possibleTables = [
            "polygondata_{$corporationId}",
            "polygondata_{$corporationId}_south_92",
            "polygondata_{$corporationId}_north_92",
            "polygondata_{$corporationId}_east_92",
            "polygondata_{$corporationId}_west_92"
        ];

        foreach ($possibleTables as $table) {
            if (Schema::hasTable($table)) {
                return $table;
            }
        }

        return "polygondata_{$corporationId}";
    }

    /**
     * Get linedata table name based on corporation, zone and ward
     */
    private function getLinedataTableName($corporationId, $zone = null, $wardNo = null)
    {
        // If we have both zone and ward, construct the specific table name
        if ($zone && $wardNo) {
            $tableName = "line_{$corporationId}_{$zone}_{$wardNo}";
            if (Schema::hasTable($tableName)) {
                return $tableName;
            }
        }

        // Try zone-specific without ward
        if ($zone) {
            $tableName = "line_{$corporationId}_{$zone}";
            if (Schema::hasTable($tableName)) {
                return $tableName;
            }
        }

        // Try to find existing table with pattern
        $possibleTables = [
            "line_{$corporationId}",
            "line_{$corporationId}_south_92",
            "line_{$corporationId}_north_92"
        ];

        foreach ($possibleTables as $table) {
            if (Schema::hasTable($table)) {
                return $table;
            }
        }

        return "line_{$corporationId}";
    }

    /**
     * Get zone and ward mapping from database
     */
    private function getZoneWardMapping($corporationId, $wardNo)
    {
        try {
            // Fetch ward from database
            $ward = Ward::where('corporation_id', $corporationId)
                ->where('ward_no', $wardNo)
                ->where('status', 'active')
                ->first();

            if ($ward) {
                return [
                    'zone' => strtolower($ward->zone),
                    'ward' => $ward->ward_no,
                    'ward_id' => $ward->id
                ];
            }
        } catch (\Exception $e) {
            \Log::error("Error fetching ward mapping: " . $e->getMessage());
        }

        // Fallback to default
        return [
            'zone' => 'south',
            'ward' => $wardNo ?? '92'
        ];
    }

    /**
     * Get all wards for dropdown/selection
     */
    public function getWards(Request $request)
    {
        $user = Auth::guard('corporation')->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $wards = Ward::where('corporation_id', $user->corporation_id)
            ->where('status', 'active')
            ->select('id', 'ward_no', 'zone', 'status')
            ->get();

        return response()->json([
            'success' => true,
            'wards' => $wards
        ]);
    }

    /**
     * Switch ward - for dashboard filtering
     */
    public function switchWard(Request $request)
    {
        $user = Auth::guard('corporation')->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $wardNo = $request->get('ward_no');

        // Verify ward exists
        $ward = Ward::where('corporation_id', $user->corporation_id)
            ->where('ward_no', $wardNo)
            ->where('status', 'active')
            ->first();

        if (!$ward) {
            return response()->json(['error' => 'Invalid ward'], 404);
        }

        // Update user's selected ward (store in session)
        session(['current_ward' => $wardNo]);

        return response()->json([
            'success' => true,
            'message' => 'Ward switched successfully',
            'ward' => $ward
        ]);
    }

    /**
     * Get dashboard stats API endpoint with ward filtering
     */
    public function getStats(Request $request)
    {
        $user = Auth::guard('corporation')->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $corporationId = $user->corporation_id;
        $wardNo = $request->get('ward_no', session('current_ward'));

        // Get ward info
        $ward = null;
        if ($wardNo) {
            $ward = Ward::where('corporation_id', $corporationId)
                ->where('ward_no', $wardNo)
                ->first();
        }

        $pointTable = $this->getPointdataTableName($corporationId, $ward->zone ?? null, $wardNo);

        try {
            $query = DB::table($pointTable);

            if ($wardNo) {
                $query->where('ward_no', $wardNo);
            }

            $totalBuildings = $query->count();
            $totalTax = $query->sum('halfyeartax');
            $totalBalance = $query->sum('balance');
            $totalWaterTax = $query->sum('water_tax');

            $collectionRate = $totalTax > 0 ? round((($totalTax - $totalBalance) / $totalTax) * 100, 2) : 0;

            $stats = [
                'total_buildings' => $totalBuildings,
                'total_tax_collected' => (float) $totalTax,
                'total_balance' => (float) $totalBalance,
                'total_water_tax' => (float) $totalWaterTax,
                'collection_rate' => $collectionRate,
                'current_ward' => $ward,
                'last_updated' => Carbon::now()->toDateTimeString()
            ];

            return response()->json($stats);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

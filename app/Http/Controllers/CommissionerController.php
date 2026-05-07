<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Corporation;
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

        // Get the corporation zone/ward mapping
        $wardNo = $user->ward_no ?? null;

        // Define table names based on corporation
        $pointdataTable = $this->getPointdataTableName($corporation->id, $wardNo);
        $polygondataTable = $this->getPolygondataTableName($corporation->id, $wardNo);
        $linedataTable = $this->getLinedataTableName($corporation->id, $wardNo);

        // Fetch all data
        $misData = $this->getMisData($corporation->id);
        $pointData = $this->getPointData($pointdataTable);
        $polygonData = $this->getPolygonData($polygondataTable);
        $lineData = $this->getLineData($linedataTable);

        // Calculate statistics
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
            'statistics' => $statistics,
            'mis_data' => $misData,
            'point_data' => $pointData,
            'polygon_data' => $polygonData,
            'line_data' => $lineData,
            'recent_activities' => $recentActivities,
            'chart_data' => $chartData,
            'top_defaulters' => $topDefaulters,
            'ward_no' => $wardNo,
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
    private function getMisData($corporationId)
    {
        try {
            $tableName = "mis_corporation_{$corporationId}";

            // Check if table exists
            if (!Schema::hasTable($tableName)) {
                return collect([]);
            }

            return DB::table($tableName)
                ->select([
                    'id', 'ward_no', 'assessment', 'old_assessment',
                    'road_name', 'owner_name', 'old_door_no', 'new_door_no',
                    'phone_number', 'plot_area', 'half_year_tax', 'balance',
                    'usage', 'type', 'zone', 'created_at', 'updated_at'
                ])
                ->orderBy('created_at', 'desc')
                ->limit(1000)
                ->get();

        } catch (\Exception $e) {
            \Log::error("Error fetching MIS data: " . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get Pointdata (building data)
     */
    private function getPointData($tableName)
    {
        try {
            if (!$tableName || !Schema::hasTable($tableName)) {
                return collect([]);
            }

            return DB::table($tableName)
                ->select([
                    'id', 'building_data_id', 'assessment_type', 'point_gisid',
                    'assessment', 'old_assessment', 'owner_name', 'present_owner_name',
                    'eb', 'floor', 'bill_usage', 'aadhar_no', 'ration_no',
                    'phone_number', 'shop_name', 'old_door_no', 'new_door_no',
                    'shop_category', 'professional_tax', 'gst', 'number_of_employee',
                    'trade_income', 'plot_area', 'water_tax', 'old_water_tax',
                    'halfyeartax', 'balance', 'qc_area', 'qc_usage', 'zone',
                    'created_at', 'updated_at'
                ])
                ->orderBy('created_at', 'desc')
                ->limit(1000)
                ->get();

        } catch (\Exception $e) {
            \Log::error("Error fetching Pointdata: " . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get Polygondata
     */
    private function getPolygonData($tableName)
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
     * Get Linedata (road/line data)
     */
    private function getLineData($tableName)
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
     * Calculate statistics for dashboard
     */
    private function calculateStatistics($misData, $pointData, $polygonData)
    {
        return [
            'total_mis_records' => $misData->count(),
            'total_buildings' => $pointData->count(),
            'total_polygons' => $polygonData->count(),

            'total_tax_collection' => $pointData->sum('halfyeartax'),
            'total_old_tax_collection' => $misData->sum('half_year_tax'),
            'total_balance' => $pointData->sum('balance') + $misData->sum('balance'),

            'total_water_tax' => $pointData->sum('water_tax'),
            'total_professional_tax' => $pointData->sum('professional_tax'),
            'total_gst' => $pointData->sum('gst'),

            'total_shops' => $pointData->whereNotNull('shop_name')->count(),
            'total_floors' => $polygonData->sum('number_floor'),
            'total_bill_connections' => $pointData->whereNotNull('eb')->count(),

            'buildings_with_cctv' => $polygonData->where('cctv', 1)->count(),
            'buildings_with_solar' => $polygonData->where('solar_panel', 1)->count(),
            'buildings_with_water_connection' => $polygonData->where('water_connection', 1)->count(),
            'buildings_with_ugd' => $polygonData->where('ugd', 1)->count(),

            'total_assessment_value' => $pointData->sum('assessment'),
            'average_tax_per_building' => $pointData->count() > 0 ? round($pointData->sum('halfyeartax') / $pointData->count(), 2) : 0,
            'collection_efficiency' => $pointData->sum('halfyeartax') > 0 ?
                round((($pointData->sum('halfyeartax') - $pointData->sum('balance')) / $pointData->sum('halfyeartax')) * 100, 2) : 0,

            'by_zone' => [
                'mis' => $misData->groupBy('zone')->map->count(),
                'buildings' => $pointData->groupBy('zone')->map->count(),
                'polygons' => $polygonData->groupBy('zone')->map->count(),
            ],

            'by_ward' => [
                'mis' => $misData->groupBy('ward_no')->map->count(),
                'buildings' => $pointData->groupBy('ward_no')->map(function($item) {
                    return $item->count();
                })
            ],

            'by_usage' => [
                'mis' => $misData->groupBy('usage')->map->count(),
                'buildings' => $pointData->groupBy('bill_usage')->map->count(),
            ],

            'by_construction_type' => $polygonData->groupBy('construction_type')->map->count(),
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
                'tax' => rand(50000, 200000), // Replace with actual data
                'balance' => rand(10000, 50000)
            ];
        }

        // Zone wise distribution
        $zoneDistribution = $pointData->groupBy('zone')->map(function($item) {
            return [
                'count' => $item->count(),
                'total_tax' => $item->sum('halfyeartax'),
                'total_balance' => $item->sum('balance')
            ];
        });

        return [
            'monthly_tax' => $monthlyTax,
            'zone_distribution' => $zoneDistribution,
            'usage_distribution' => $pointData->groupBy('bill_usage')->map->count(),
            'floor_distribution' => $pointData->groupBy('floor')->map->count()
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
                    'assessment' => $item->assessment,
                    'balance' => $item->balance,
                    'phone' => $item->phone_number,
                    'type' => 'Building'
                ];
            });

        // Get from MIS data
        $misDefaulters = $misData->where('balance', '>', 0)
            ->sortByDesc('balance')
            ->take(10)
            ->map(function($item) {
                return (object)[
                    'name' => $item->owner_name ?? 'Unknown',
                    'assessment' => $item->assessment,
                    'balance' => $item->balance,
                    'phone' => $item->phone_number,
                    'type' => 'MIS Record'
                ];
            });

        return $pointDefaulters->concat($misDefaulters)->sortByDesc('balance')->take(10);
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
                'assessment' => $item->assessment,
                'location' => "Door No: " . ($item->new_door_no ?? $item->old_door_no ?? 'N/A'),
                'date' => $item->updated_at ?? $item->created_at,
                'status' => $item->balance > 0 ? 'pending' : 'paid',
                'balance' => $item->balance ?? 0
            ];
        });

        // Add recent polygondata updates
        $recentPolygons = $polygonData->take(20)->map(function($item) {
            return [
                'type' => 'Property Update',
                'icon' => 'fa-map-marker-alt',
                'description' => "Property: " . ($item->building_name ?? 'N/A'),
                'assessment' => $item->sqfeet,
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
     * Get summary by ward
     */
    public function getWardSummary(Request $request)
    {
        $user = Auth::guard('corporation')->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $corporationId = $user->corporation_id;

        try {
            // Get MIS data grouped by ward
            $misTable = "mis_corporation_{$corporationId}";
            $misSummary = DB::table($misTable)
                ->select('ward_no',
                    DB::raw('COUNT(*) as total_records'),
                    DB::raw('SUM(half_year_tax) as total_tax'),
                    DB::raw('SUM(balance) as total_balance'),
                    DB::raw('AVG(assessment) as avg_assessment'))
                ->groupBy('ward_no')
                ->get();

            // Get point data grouped by ward
            $pointTable = $this->getPointdataTableName($corporationId);
            $pointSummary = DB::table($pointTable)
                ->select('ward_no',
                    DB::raw('COUNT(*) as total_buildings'),
                    DB::raw('SUM(halfyeartax) as total_tax'),
                    DB::raw('SUM(balance) as total_balance'),
                    DB::raw('SUM(water_tax) as total_water_tax'))
                ->groupBy('ward_no')
                ->get();

            return response()->json([
                'success' => true,
                'mis_summary' => $misSummary,
                'point_summary' => $pointSummary
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export data to CSV/Excel
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
                // For Excel/PDF, you can implement using Maatwebsite/Excel package
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
     * Get pointdata table name based on corporation and ward
     */
    private function getPointdataTableName($corporationId, $wardNo = null)
    {
        $zoneWard = $this->getZoneWardMapping($corporationId, $wardNo);

        if ($zoneWard && isset($zoneWard['zone']) && isset($zoneWard['ward'])) {
            return "pointdata_{$corporationId}_{$zoneWard['zone']}_{$zoneWard['ward']}";
        }

        // Try to find existing table
        $possibleTables = [
            "pointdata_{$corporationId}",
            "pointdata_{$corporationId}_south_92",
            "pointdata_{$corporationId}_north_92"
        ];

        foreach ($possibleTables as $table) {
            if (Schema::hasTable($table)) {
                return $table;
            }
        }

        return "pointdata_{$corporationId}";
    }

    /**
     * Get polygondata table name based on corporation and ward
     */
    private function getPolygondataTableName($corporationId, $wardNo = null)
    {
        $zoneWard = $this->getZoneWardMapping($corporationId, $wardNo);

        if ($zoneWard && isset($zoneWard['zone']) && isset($zoneWard['ward'])) {
            return "polygondata_{$corporationId}_{$zoneWard['zone']}_{$zoneWard['ward']}";
        }

        $possibleTables = [
            "polygondata_{$corporationId}",
            "polygondata_{$corporationId}_south_92",
            "polygondata_{$corporationId}_north_92"
        ];

        foreach ($possibleTables as $table) {
            if (Schema::hasTable($table)) {
                return $table;
            }
        }

        return "polygondata_{$corporationId}";
    }

    /**
     * Get linedata table name based on corporation and ward
     */
    private function getLinedataTableName($corporationId, $wardNo = null)
    {
        $zoneWard = $this->getZoneWardMapping($corporationId, $wardNo);

        if ($zoneWard && isset($zoneWard['zone']) && isset($zoneWard['ward'])) {
            return "line_{$corporationId}_{$zoneWard['zone']}_{$zoneWard['ward']}";
        }

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
     * Get zone and ward mapping
     */
    private function getZoneWardMapping($corporationId, $wardNo)
    {
        // You can fetch from database configuration
        // For now, returning based on common pattern
        $mapping = [
            'zone' => 'south',
            'ward' => $wardNo ?? '92'
        ];

        return $mapping;
    }

    /**
     * Get dashboard stats API endpoint
     */
    public function getStats(Request $request)
    {
        $user = Auth::guard('corporation')->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $corporationId = $user->corporation_id;
        $pointTable = $this->getPointdataTableName($corporationId);

        $stats = [
            'total_buildings' => DB::table($pointTable)->count(),
            'total_tax_collected' => DB::table($pointTable)->sum('halfyeartax'),
            'total_balance' => DB::table($pointTable)->sum('balance'),
            'total_water_tax' => DB::table($pointTable)->sum('water_tax'),
            'collection_rate' => $this->calculateStatistics(collect([]), DB::table($pointTable)->get(), collect([]))['collection_efficiency']
        ];

        return response()->json($stats);
    }
}

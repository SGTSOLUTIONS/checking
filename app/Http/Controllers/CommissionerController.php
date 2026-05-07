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

        // Fetch all data
        $misData = $this->getMisData($corporation->id, $wardNo);
        $pointData = $this->getPointData($pointdataTable);
        $polygonData = $this->getPolygonData($polygondataTable);
        $lineData = $this->getLineData($linedataTable);

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
     * Get MIS data for the corporation - Updated to filter by ward
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
     * Get Pointdata (building data) - Updated to include ward filtering
     */
    private function getPointData($tableName, $wardNo = null)
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

        // Update user's selected ward (you might want to store this in session or user table)
        session(['current_ward' => $wardNo]);

        return response()->json([
            'success' => true,
            'message' => 'Ward switched successfully',
            'ward' => $ward
        ]);
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

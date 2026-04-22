<?php

namespace App\Http\Controllers;

use App\Models\TeamMember;
use App\Models\Team;
use App\Services\GeoDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SurveyorController extends Controller
{
    protected $geoDataService;

    public function __construct(GeoDataService $geoDataService)
    {
        $this->geoDataService = $geoDataService;
    }

    public function index()
    {
        $userId = Auth::id();

        // Check if surveyor belongs to any team and get team data with ward
        $teamMember = TeamMember::with(['team.ward', 'team.corporation'])
            ->where('user_id', $userId)
            ->first();

        if (!$teamMember) {
            // Return view with no team message
            return view('surveyor.dashboard', [
                'hasTeam' => false,
                'message' => 'You are not assigned to any team. Please contact your team leader.'
            ]);
        }

        // Return view with team and ward data
        return view('surveyor.dashboard', [
            'hasTeam' => true,
            'team' => $teamMember->team,
            'ward' => $teamMember->team->ward,
            'corporation' => $teamMember->team->corporation,
            'userRole' => $teamMember->role
        ]);
    }

    public function mapView()
    {
        $userId = Auth::id();
        $teamMember = TeamMember::with(['team.ward'])
            ->where('user_id', $userId)
            ->first();

        if (!$teamMember) {
            return view('surveyor.dashboard', [
                'hasTeam' => false,
                'message' => 'You are not assigned to any team.',
            ]);
        }

        $ward = $teamMember->team->ward;
        $zone = strtolower(trim($ward->zone));
        $wardNo = (int)$ward->ward_no;
        $corp = (int)$ward->corporation_id;

        // Table names
        $polygonsTableName = "polygon_{$corp}_{$zone}_{$wardNo}";
        $polygonDataTableName = "polygondata_{$corp}_{$zone}_{$wardNo}";
        $pointsTableName = "point_{$corp}_{$zone}_{$wardNo}";
        $pointDataTableName = "pointdata_{$corp}_{$zone}_{$wardNo}";
        $linesTableName = "line_{$corp}_{$zone}_{$wardNo}";
        $misTableName = "mis_corporation_{$corp}";

        // Get data
        $polygons = DB::table($polygonsTableName)->get();
        $lines = DB::table($linesTableName)->get();
        $points = DB::table($pointsTableName)->get();
        $polygonDatas = DB::table($polygonDataTableName)->get();
        $pointDatas = DB::table($pointDataTableName)->get();
        $misData = DB::table($misTableName)->get();

        // Get unique road names from misData
        $uniqueRoadNames = DB::table($misTableName)
            ->select('road_name')
            ->whereNotNull('road_name')
            ->where('road_name', '!=', '')
            ->distinct()
            ->orderBy('road_name')
            ->pluck('road_name');

        return view('surveyor.mapview', compact(
            'teamMember',
            'ward',
            'polygons',
            'points',
            'lines',
            'polygonDatas',
            'pointDatas',
            'misData',
            'uniqueRoadNames' // Add this to the compact array
        ));
    }
    public function addPolygonFeature(Request $request)
    {
        $data = $request->all();
        $userId = Auth::id();

        $teamMember = TeamMember::with(['team.ward'])
            ->where('user_id', $userId)
            ->first();

        if (!$teamMember) {
            return view('surveyor.dashboard', [
                'hasTeam' => false,
                'message' => 'You are not assigned to any team.',
            ]);
        }

        $ward = $teamMember->team->ward;
        $zone = strtolower(trim($ward->zone));
        $wardNo = (int)$ward->ward_no;
        $corp = (int)$ward->corporation_id;

        // Dynamic table names
        $polygonsTableName = "polygon_{$corp}_{$zone}_{$wardNo}";
        $pointsTableName   = "point_{$corp}_{$zone}_{$wardNo}";

        // Insert polygon/point
        $polygonProcessResult = $this->geoDataService->storeSinglePolygon(
            $polygonsTableName,
            $pointsTableName,
            $data
        );

        // Fetch all polygons and points from the tables
        $allPolygons = DB::table($polygonsTableName)->get();
        $allPoints = DB::table($pointsTableName)->get();

        return response()->json([
            'success'  => $polygonProcessResult['success'],
            'message'  => $polygonProcessResult['message'],
            'polygons' => $allPolygons,
            'points'   => $allPoints,
        ]);
    }
    public function modifyFeature(Request $request)
    {
        $data = $request->all();
        $userId = Auth::id();

        $teamMember = TeamMember::with(['team.ward'])
            ->where('user_id', $userId)
            ->first();

        if (!$teamMember) {
            return view('surveyor.dashboard', [
                'hasTeam' => false,
                'message' => 'You are not assigned to any team.',
            ]);
        }

        $ward = $teamMember->team->ward;
        $zone = strtolower(trim($ward->zone));
        $wardNo = (int)$ward->ward_no;
        $corp = (int)$ward->corporation_id;

        // Dynamic table names


        if ($data['type'] === 'LineString' || $data['type'] === 'Line') {

            $linesTableName = "line_{$corp}_{$zone}_{$wardNo}";

            // Extract GIS_ID from request
            $gisid = $data['properties']['GIS_ID']
                ?? $data['properties']['gisid']
                ?? $data['gisid']
                ?? null;

            if (!$gisid) {
                return response()->json([
                    'success' => false,
                    'message' => 'GIS_ID missing in request'
                ], 400);
            }

            // Extract geometry details
            $coords = $data['coordinates'] ?? null;
            $geometryType = "LineString"; // Always correct for DB

            // Decode if string
            if (is_string($coords)) {
                $coords = json_decode($coords, true);
                if ($coords === null) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid coordinates JSON'
                    ], 400);
                }
            }

            // Update line feature
            $updated = DB::table($linesTableName)
                ->where('gisid', $gisid)
                ->update([
                    'type' => $geometryType,
                    'coordinates' => json_encode($coords, JSON_NUMERIC_CHECK),
                    'updated_at' => now(),
                ]);

            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => "No record found for GIS_ID: $gisid"
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Line updated successfully',
                'lines' => DB::table($linesTableName)->get()
            ]);
        } elseif ($data['type'] === 'Polygon') {
            $polygonsTableName = "polygon_{$corp}_{$zone}_{$wardNo}";
            $pointsTableName   = "point_{$corp}_{$zone}_{$wardNo}";
            $polygonProcessResult = $this->geoDataService->updateSinglePolygon(
                $polygonsTableName,
                $pointsTableName,
                $data,

            );
            // Fetch all polygons and points from the tables
            $allPolygons = DB::table($polygonsTableName)->get();
            $allPoints = DB::table($pointsTableName)->get();

            return response()->json([
                'success'  => $polygonProcessResult['success'],
                'message'  => $polygonProcessResult['message'],
                'polygons' => $allPolygons,
                'points'   => $allPoints,
            ]);
        }

        // Insert polygon/point

    }
    public function addLineFeature(Request $request)
    {
        $data = $request->all();
        $userId = Auth::id();

        $teamMember = TeamMember::with(['team.ward'])
            ->where('user_id', $userId)
            ->first();

        if (!$teamMember) {
            return view('surveyor.dashboard', [
                'hasTeam' => false,
                'message' => 'You are not assigned to any team.',
            ]);
        }

        $ward = $teamMember->team->ward;
        $zone = strtolower(trim($ward->zone));
        $wardNo = (int)$ward->ward_no;
        $corp = (int)$ward->corporation_id;

        // Dynamic table name
        $linesTableName = "line_{$corp}_{$zone}_{$wardNo}";

        // Extract and decode coordinates from request
        $coords = $data['coordinates'] ?? null;
        if (!$coords) {
            return ['success' => false, 'message' => 'Missing coordinates'];
        }

        if (is_string($coords)) {

            $coords = json_decode($coords, true);
            if ($coords === null) {
                return ['success' => false, 'message' => 'Invalid coordinates JSON'];
            }
        }

        // Generate GIS_ID
        $allIds = DB::table($linesTableName)->pluck('gisid');
        $maxNumber = 0;
        $prefix = '';

        foreach ($allIds as $id) {
            if (preg_match_all('/\d+/', $id, $matches)) {
                $numbers = $matches[0];
                $lastNum = (int)end($numbers);
                if ($lastNum > $maxNumber) {
                    $maxNumber = $lastNum;
                    $prefix = substr($id, 0, strrpos($id, (string)$lastNum));
                }
            }
        }

        $newGisNumber = $maxNumber + 1;
        $gisid = $prefix . $newGisNumber;

        // Insert new line feature
        DB::table($linesTableName)->insert([
            'gisid' => $gisid,
            'type' => 'LineString', // Always correct GeoJSON type
            'coordinates' => json_encode([$coords], JSON_NUMERIC_CHECK),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $lines = DB::table($linesTableName)->get();

        return [

            'lines' => $lines,

        ];
    }
    public function deleteFeature(Request $request)
    {
        $data = $request->all();
        $userId = Auth::id();

        $teamMember = TeamMember::with(['team.ward'])
            ->where('user_id', $userId)
            ->first();

        if (!$teamMember) {
            return view('surveyor.dashboard', [
                'hasTeam' => false,
                'message' => 'You are not assigned to any team.',
            ]);
        }

        $ward = $teamMember->team->ward;
        $zone = strtolower(trim($ward->zone));
        $wardNo = (int)$ward->ward_no;
        $corp = (int)$ward->corporation_id;

        // Dynamic table names
        $polygonsTableName = "polygon_{$corp}_{$zone}_{$wardNo}";
        $pointsTableName   = "point_{$corp}_{$zone}_{$wardNo}";

        $deleteResult = $this->geoDataService->deleteFeatureByGisId(
            $polygonsTableName,
            $pointsTableName,
            $data['gisid']
        );

        $polygons = DB::table($polygonsTableName)->get();
        $points   = DB::table($pointsTableName)->get();

        return response()->json([
            'success'  => $deleteResult['success'],
            'message'  => $deleteResult['message'],
            'polygons' => $polygons,
            'points'   => $points,
        ]);
    }
    public function updateRoadName(Request $request)
    {
        $data = $request->all();
        $userId = Auth::id();

        $teamMember = TeamMember::with(['team.ward'])
            ->where('user_id', $userId)
            ->first();

        if (!$teamMember) {
            return view('surveyor.dashboard', [
                'hasTeam' => false,
                'message' => 'You are not assigned to any team.',
            ]);
        }

        $ward = $teamMember->team->ward;
        $zone = strtolower(trim($ward->zone));
        $wardNo = (int)$ward->ward_no;
        $corp = (int)$ward->corporation_id;

        // Dynamic table names
        $linesTableName = "line_{$corp}_{$zone}_{$wardNo}";
        $gisid = $data['gisid'] ?? null;
        $newRoadName = $data['road_name'] ?? null;
        if (!$gisid || !$newRoadName) {
            return response()->json([
                'success' => false,
                'message' => 'GIS_ID and new road name are required'
            ], 400);
        } else {
            $updated = DB::table($linesTableName)
                ->where('gisid', $gisid)
                ->update([
                    'road_name' => $newRoadName,
                    'updated_at' => now(),
                ]);

            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => "No record found for GIS_ID: $gisid"
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Road name updated successfully',
                'lines' => DB::table($linesTableName)->get()
            ]);
        }
        return response()->json([$request->all()]);
    }
    // progress of surveyor
    public function progress()
    {
        $userId = Auth::id();

        $teamMember = TeamMember::with(['team.ward'])
            ->where('user_id', $userId)
            ->first();

        if (!$teamMember) {
            return view('surveyor.progress', [
                'hasTeam' => false,
                'message' => 'You are not assigned to any team. Please contact your team leader.'
            ]);
        }

        $ward = $teamMember->team->ward;
        $zone = strtolower(trim($ward->zone));
        $wardNo = (int)$ward->ward_no;
        $corp = (int)$ward->corporation_id;

        // Get worker name for the logged-in user
        $workerName = $teamMember->user->id . '-' . $teamMember->user->name;

        // Table names
        $polygonDataTableName = "polygondata_{$corp}_{$zone}_{$wardNo}";
        $pointDataTableName = "pointdata_{$corp}_{$zone}_{$wardNo}";
        $polygonTableName = "polygon_{$corp}_{$zone}_{$wardNo}";
        $pointTableName = "point_{$corp}_{$zone}_{$wardNo}";

        // MIS Master Data table
        $misTableName = "mis_corporation_{$corp}";

        // Get all MIS records for this ward
        $misRecords = DB::table($misTableName)
            ->where('ward_no', $wardNo)
            ->whereNull('deleted_at')
            ->get();

        $totalMisRecords = $misRecords->count();

        // Get unique road names from MIS data
        $misRoadNames = DB::table($misTableName)
            ->where('ward_no', $wardNo)
            ->whereNull('deleted_at')
            ->whereNotNull('road_name')
            ->where('road_name', '!=', '')
            ->select('road_name', DB::raw('COUNT(*) as total_properties'))
            ->groupBy('road_name')
            ->orderBy('road_name')
            ->get();

        // Get surveyed buildings by this worker
        $surveyedBuildings = DB::table($polygonDataTableName)
            ->where('worker_name', $workerName)
            ->get();
        $surveyedBuildingsCount = $surveyedBuildings->count();

        // Get surveyed points by this worker
        $surveyedPoints = DB::table($pointDataTableName)
            ->where('worker_name', $workerName)
            ->get();
        $surveyedPointsCount = $surveyedPoints->count();

        // Track surveyed GIS IDs
        $surveyedGisIds = $surveyedBuildings->pluck('gisid')->toArray();

        // Find pending properties from MIS that are not surveyed yet
        $pendingProperties = DB::table($misTableName)
            ->where('ward_no', $wardNo)
            ->whereNull('deleted_at')
            ->whereNotIn('assessment', function ($query) use ($polygonDataTableName) {
                $query->select('gisid')->from($polygonDataTableName);
            })
            ->get();

        $pendingPropertiesCount = $pendingProperties->count();

        // Calculate completion percentage based on MIS data
        $misCompletionPercentage = $totalMisRecords > 0
            ? round(($surveyedBuildingsCount / $totalMisRecords) * 100, 2)
            : 0;

        // Road-wise statistics comparing MIS data with surveyed data
        $roadWiseStats = [];
        $roadWiseStats = DB::table($polygonDataTableName)
            ->select('road_name', DB::raw('COUNT(*) as surveyed_buildings'))
            ->where('worker_name', $workerName)
            ->whereNotNull('road_name')
            ->where('road_name', '!=', '')
            ->groupBy('road_name')
            ->orderBy('road_name')
            ->get();

        // For each surveyed road, get additional statistics
        foreach ($roadWiseStats as $stat) {
            // Get buildings on this road surveyed by this worker
            $buildingsOnRoad = DB::table($polygonDataTableName)
                ->where('road_name', $stat->road_name)
                ->where('worker_name', $workerName)
                ->pluck('gisid')
                ->toArray();

            // Count points surveyed on this road
            $stat->points_surveyed = DB::table($pointDataTableName)
                ->where('worker_name', $workerName)
                ->whereIn('point_gisid', $buildingsOnRoad)
                ->count();

            // Get total expected points (number of bills) for buildings on this road
            $stat->expected_points = DB::table($polygonDataTableName)
                ->where('road_name', $stat->road_name)
                ->where('worker_name', $workerName)
                ->sum('number_bill');

            // Get total properties from MIS for this road (optional - for comparison)
            $stat->total_properties = DB::table($misTableName)
                ->where('ward_no', $wardNo)
                ->where('road_name', $stat->road_name)
                ->whereNull('deleted_at')
                ->count();

            // Calculate completion percentage
            $stat->completion_percentage = $stat->expected_points > 0
                ? round(($stat->points_surveyed / $stat->expected_points) * 100, 2)
                : 0;
        }

        // Building usage statistics from surveyed data
        $buildingUsageStats = DB::table($polygonDataTableName)
            ->select('building_usage', DB::raw('COUNT(*) as count'))
            ->where('worker_name', $workerName)
            ->whereNotNull('building_usage')
            ->groupBy('building_usage')
            ->get();

        // Compare with MIS usage data
        $misUsageStats = DB::table($misTableName)
            ->where('ward_no', $wardNo)
            ->whereNull('deleted_at')
            ->select('usage', DB::raw('COUNT(*) as count'))
            ->whereNotNull('usage')
            ->groupBy('usage')
            ->get();

        // Construction type statistics
        $constructionTypeStats = DB::table($polygonDataTableName)
            ->select('construction_type', DB::raw('COUNT(*) as count'))
            ->where('worker_name', $workerName)
            ->whereNotNull('construction_type')
            ->groupBy('construction_type')
            ->get();

        // Building type statistics
        $buildingTypeStats = DB::table($polygonDataTableName)
            ->select('building_type', DB::raw('COUNT(*) as count'))
            ->where('worker_name', $workerName)
            ->whereNotNull('building_type')
            ->groupBy('building_type')
            ->orderBy('count', 'desc')
            ->get();

        // Amenities statistics
        $amenities = ['liftroom', 'headroom', 'overhead_tank', 'rainwater_harvesting', 'parking', 'ramp', 'hoarding', 'cctv', 'cell_tower', 'solar_panel'];
        $amenitiesStats = [];

        foreach ($amenities as $amenity) {
            $amenitiesStats[$amenity] = [
                'yes' => DB::table($polygonDataTableName)->where('worker_name', $workerName)->where($amenity, 'Yes')->count(),
                'no' => DB::table($polygonDataTableName)->where('worker_name', $workerName)->where($amenity, 'No')->count(),
            ];
        }

        // UGD connection statistics
        $ugdStats = DB::table($polygonDataTableName)
            ->select('ugd', DB::raw('COUNT(*) as count'))
            ->where('worker_name', $workerName)
            ->whereNotNull('ugd')
            ->groupBy('ugd')
            ->get();

        // Recent surveys
        $recentSurveys = DB::table($polygonDataTableName)
            ->where('worker_name', $workerName)
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        // Buildings with point data details
        $buildingsWithPoints = [];
        foreach ($surveyedBuildings as $building) {
            $pointsCount = DB::table($pointDataTableName)
                ->where('point_gisid', $building->gisid)
                ->where('worker_name', $workerName)
                ->count();

            $expectedPoints = $building->number_bill ?? 0;

            // Find matching MIS record
            $misRecord = $misRecords->firstWhere('assessment', $building->gisid);

            $buildingsWithPoints[] = (object)[
                'gisid' => $building->gisid,
                'building_name' => $building->building_name,
                'road_name' => $building->road_name,
                'expected_bills' => $expectedPoints,
                'surveyed_bills' => $pointsCount,
                'completion_percentage' => $expectedPoints > 0 ? round(($pointsCount / $expectedPoints) * 100, 2) : 0,
                'status' => $pointsCount >= $expectedPoints ? 'Completed' : 'Pending',
                'mis_owner_name' => $misRecord->owner_name ?? 'N/A',
                'last_updated' => $building->updated_at
            ];
        }

        // Points with building details
        $pointsWithDetails = DB::table($pointDataTableName)
            ->where($pointDataTableName . '.worker_name', $workerName)
            ->leftJoin($polygonDataTableName, $pointDataTableName . '.point_gisid', '=', $polygonDataTableName . '.gisid')
            ->select(
                $pointDataTableName . '.*',
                $polygonDataTableName . '.building_name',
                $polygonDataTableName . '.road_name'
            )
            ->orderBy($pointDataTableName . '.created_at', 'desc')
            ->limit(20)
            ->get();

        // Overall progress summary
        $overallProgress = [
            'worker_name' => $workerName,
            'total_mis_records' => $totalMisRecords,
            'surveyed_buildings' => $surveyedBuildingsCount,
            'pending_properties' => $pendingPropertiesCount,
            'mis_completion_percentage' => $misCompletionPercentage,
            'total_points' => DB::table($pointTableName)->count(),
            'surveyed_points' => $surveyedPointsCount,
            'point_completion_percentage' => DB::table($pointTableName)->count() > 0
                ? round(($surveyedPointsCount / DB::table($pointTableName)->count()) * 100, 2)
                : 0,
            'total_expected_bills' => DB::table($polygonDataTableName)->where('worker_name', $workerName)->sum('number_bill'),
            'total_bills_surveyed' => $surveyedPointsCount,
        ];

        // Daily progress
        $dailyProgress = DB::table($polygonDataTableName)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as buildings_surveyed'))
            ->where('worker_name', $workerName)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();

        foreach ($dailyProgress as $daily) {
            $daily->points_surveyed = DB::table($pointDataTableName)
                ->where('worker_name', $workerName)
                ->whereDate('created_at', $daily->date)
                ->count();
        }

        // Pending properties list from MIS
        $pendingPropertiesList = DB::table($misTableName)
            ->where('ward_no', $wardNo)
            ->whereNull('deleted_at')
            ->whereNotIn('assessment', function ($query) use ($polygonDataTableName) {
                $query->select('gisid')->from($polygonDataTableName);
            })
            ->select('assessment', 'old_assessment', 'owner_name', 'road_name', 'old_door_no', 'new_door_no', 'phone_number')
            ->orderBy('road_name')
            ->limit(50)
            ->get();

        return view('surveyor.progress', compact(
            'teamMember',
            'ward',
            'workerName',
            'overallProgress',
            'roadWiseStats',
            'buildingUsageStats',
            'misUsageStats',
            'constructionTypeStats',
            'buildingTypeStats',
            'amenitiesStats',
            'ugdStats',
            'recentSurveys',
            'buildingsWithPoints',
            'pointsWithDetails',
            'surveyedBuildings',
            'surveyedPoints',
            'dailyProgress',
            'pendingPropertiesList',
            'totalMisRecords',
            'pendingPropertiesCount',
            'misCompletionPercentage'
        ));
    }


    // store polygon data
    public function uploadPolygonData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // Required basic fields
            'building_gisid' => 'required|string|max:50',
            'number_bill' => 'nullable|integer',
            'number_shop' => 'required|integer|min:0',
            'number_floor' => 'required|integer|min:0',
            'building_name' => 'nullable|string|max:255',
            'new_address' => 'nullable|string|max:500',
            'road_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'building_usage' => 'required|in:RESIDENTIAL,COMMERCIAL,MIXED,INDUSTRIAL,INSTITUTIONAL,GOVERNMENT,VACANT',
            'construction_type' => 'required|in:PERMANENT,SEMI_PERMANENT,VACANT_LAND,SHED,CAR_SHED,TEMPORARY',
            'building_type' => 'required|in:Independent,Flat,Kalyana_Mandapam,Hotel,Cinema_Theatre,Central_Government_Building,State_Government_Building,Municipality_Corporation,Educational_Institution,Hospital,Commercial_Complex,Shop,Office,Temple,Mosque,Church,Amma_Unavagam,Public_Toilet,Vacant Land,Under Construction,Others',
            'ugd' => 'nullable|in:No_Connection,Manhole_Available_but_Connection_Not_Given_to_House,Stage_1_Completed,Stage_1_2_Completed,Stage_1_2_Completed_but_Not_Connected,Stage_1_2_3_Completed,Direct_Connection_Given,1_UGD_Connection_-_3_Stage_Completed,2_UGD_Connection_-_3_Stage_Completed',
            'liftroom' => 'nullable|in:Yes,No',
            'headroom' => 'nullable|in:Yes,No',
            'overhead_tank' => 'nullable|in:Yes,No',
            'rainwater_harvesting' => 'nullable|in:Yes,No',
            'parking' => 'nullable|in:Yes,No',
            'ramp' => 'nullable|in:Yes,No',
            'hoarding' => 'nullable|in:Yes,No',
            'cctv' => 'nullable|in:Yes,No',
            'cell_tower' => 'nullable|in:Yes,No',
            'solar_panel' => 'nullable|in:Yes,No',
            'basement' => 'required|integer|min:0|max:5',
            'water_connection' => 'nullable',
            'percentage' => 'required|numeric|min:0|max:100',
            'remarks' => 'nullable|string|max:500',
            'corporationremarks' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'image2' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->all();
        $userId = Auth::id();

        $teamMember = TeamMember::with(['team.ward'])
            ->where('user_id', $userId)
            ->first();

        if (!$teamMember) {
            return response()->json([
                'success' => false,
                'message' => 'You are not assigned to any team.',
            ], 403);
        }

        $ward = $teamMember->team->ward;
        $zone = strtolower(trim($ward->zone));
        $wardNo = (int)$ward->ward_no;
        $corp = (int)$ward->corporation_id;
        $polygonDataTableName = "polygondata_{$corp}_{$zone}_{$wardNo}";

        // Check if record exists
        $existingRecord = DB::table($polygonDataTableName)->where('gisid', $data['building_gisid'])->first();

        // Validate Flat building condition
        if ($request->building_type == "Flat" && $request->number_floor < 3) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => [
                    'number_floor' => ['Flat building must have at least 3 floors.'],
                    'building_type' => ['If building type is Flat, number of floors must be at least 3.']
                ]
            ], 422);
        }

        // Handle image uploads
        $imagePath1 = null;
        $imagePath2 = null;

        // Create directory if not exists
        $uploadDir = public_path("uploads/{$corp}/{$polygonDataTableName}");
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Handle first image (image column)
        if ($request->hasFile('image')) {
            $image1 = $request->file('image');
            $fileName1 = $data['building_gisid'] . '_image1.' . $image1->getClientOriginalExtension();
            $image1->move($uploadDir, $fileName1);
            $imagePath1 = "uploads/{$corp}/{$polygonDataTableName}/{$fileName1}";
        }

        // Handle second image (image2 column)
        if ($request->hasFile('image2')) {
            $image2 = $request->file('image2');
            $fileName2 = $data['building_gisid'] . '_image2.' . $image2->getClientOriginalExtension();
            $image2->move($uploadDir, $fileName2);
            $imagePath2 = "uploads/{$corp}/{$polygonDataTableName}/{$fileName2}";
        }

        // Prepare data for insert/update
        $insertData = [
            'gisid' => $data['building_gisid'],
            'number_bill' => $data['number_bill'] ?? null,
            'number_shop' => $data['number_shop'],
            'number_floor' => $data['number_floor'],
            'building_name' => $data['building_name'] ?? null,
            'new_address' => $data['new_address'] ?? null,
            'road_name' => $data['road_name'],
            'phone' => $data['phone'] ?? null,
            'building_usage' => $data['building_usage'],
            'construction_type' => $data['construction_type'],
            'building_type' => $data['building_type'],
            'ugd' => $data['ugd'] ?? null,
            'liftroom' => $data['liftroom'] ?? 'No',
            'headroom' => $data['headroom'] ?? 'No',
            'overhead_tank' => $data['overhead_tank'] ?? 'No',
            'rainwater_harvesting' => $data['rainwater_harvesting'] ?? 'No',
            'parking' => $data['parking'] ?? 'No',
            'ramp' => $data['ramp'] ?? 'No',
            'hoarding' => $data['hoarding'] ?? 'No',
            'cctv' => $data['cctv'] ?? 'No',
            'cell_tower' => $data['cell_tower'] ?? 'No',
            'solar_panel' => $data['solar_panel'] ?? 'No',
            'basement' => $data['basement'],
            'water_connection' => $data['water_connection'] ?? null,
            'percentage' => $data['percentage'],
            'remarks' => $data['remarks'] ?? null,
            'worker_name' => $teamMember->user->id . '-' . $teamMember->user->name,
            'corporationremarks' => $data['corporationremarks'] ?? null,
            'updated_at' => now(),
        ];

        // Add image paths only if new images were uploaded (using correct column names: 'image' and 'image2')
        if ($imagePath1) {
            $insertData['image'] = $imagePath1;
        }
        if ($imagePath2) {
            $insertData['image2'] = $imagePath2;
        }

        try {
            if ($existingRecord) {
                // UPDATE existing record
                // If no new images uploaded, keep existing images (using correct column names)
                if (!$imagePath1 && isset($existingRecord->image) && $existingRecord->image) {
                    $insertData['image'] = $existingRecord->image;
                }
                if (!$imagePath2 && isset($existingRecord->image2) && $existingRecord->image2) {
                    $insertData['image2'] = $existingRecord->image2;
                }

                DB::table($polygonDataTableName)->where('gisid', $data['building_gisid'])->update($insertData);

                // Delete old images if new ones were uploaded (using correct column names)
                if ($imagePath1 && isset($existingRecord->image) && $existingRecord->image) {
                    $oldImagePath = public_path($existingRecord->image);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
                if ($imagePath2 && isset($existingRecord->image2) && $existingRecord->image2) {
                    $oldImagePath2 = public_path($existingRecord->image2);
                    if (file_exists($oldImagePath2)) {
                        unlink($oldImagePath2);
                    }
                }

                $message = 'Building data updated successfully';
            } else {
                // INSERT new record
                $insertData['created_at'] = now();
                DB::table($polygonDataTableName)->insert($insertData);
                $message = 'Building data saved successfully';
            }

            // Fetch updated data to return
            $updatedRecord = DB::table($polygonDataTableName)->where('gisid', $data['building_gisid'])->first();

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $updatedRecord,
                'polygonDatas' => DB::table($polygonDataTableName)->get()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ], 500);
        }
    }
    public function uploadPointData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:OLD,NEW,OTHER',
            'point_gisid' => 'required|string|max:50',
            'assessment' => 'nullable|string|max:100',
            'old_assessment' => 'nullable|string|max:100',
            'owner_name' => 'required|string|max:255',
            'present_owner_name' => 'nullable|string|max:255',
            'no_of_shop' => 'required|integer|min:0',
            'floor' => 'required|integer|min:0',
            'old_door_no' => 'nullable|string|max:50',
            'number_persons' => 'nullable',
            'new_door_no' => 'nullable|string|max:50',
            'bill_usage' => 'nullable|in:Residential,Commercial,Mixed',
            'eb' => 'nullable|string|max:50',
            'water_tax' => 'nullable|string|max:100',
            'old_water_tax' => 'nullable|string|max:100',
            'professional_tax' => 'nullable|string|max:100',
            'gst' => 'nullable|string|max:50',
            'trade_income' => 'nullable|numeric|min:0',
            'aadhar_no' => 'nullable|string|max:12',
            'ration_no' => 'nullable|string|max:50',
            'phone_number' => 'nullable|string|max:10',
            'qc_area' => 'nullable|string|max:100',
            'qc_usage' => 'nullable|string|max:100',
            'qc_name' => 'nullable|string|max:255',
            'qc_remarks' => 'nullable|string|max:500',
            'establishment_remarks' => 'nullable|string|max:500',
            'remarks' => 'nullable|string|max:500',
            'total_shops' => 'required|integer|min:0',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->all();
        $userId = Auth::id();

        $teamMember = TeamMember::with(['team.ward'])
            ->where('user_id', $userId)
            ->first();

        if (!$teamMember) {
            return response()->json([
                'success' => false,
                'message' => 'You are not assigned to any team.',
            ], 403);
        }

        $ward = $teamMember->team->ward;
        $zone = strtolower(trim($ward->zone));
        $wardNo = (int)$ward->ward_no;
        $corp = (int)$ward->corporation_id;

        $polygonDataTableName = "polygondata_{$corp}_{$zone}_{$wardNo}";
        $pointDataTableName = "pointdata_{$corp}_{$zone}_{$wardNo}";
        $shopDataTableName = "shopdata_{$corp}_{$zone}_{$wardNo}";

        // Check if point already exists for this GIS ID
        $existingPoint = DB::table($pointDataTableName)
            ->where('point_gisid', $data['point_gisid'])
            ->first();

        // Find the building_data_id from polygon data
        $buildingData = DB::table($polygonDataTableName)
            ->where('gisid', $data['point_gisid'])
            ->first();
        if (!$buildingData) {
            return response()->json([
                'success' => false,
                'message' => 'Building data not found for this GIS ID. Please add building data first.',
            ], 404);
        }
        if ($existingPoint) {
            $shopdatacount = DB::table($shopDataTableName)
                ->where('point_data_id', $existingPoint->id)
                ->count();

            if (($data['no_of_shop'] + $shopdatacount) > $buildingData->number_shop) {

                $remaining = $buildingData->number_shop - $shopdatacount;

                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => [
                        'no_of_shop' => [
                            "Only $remaining shops can be added"
                        ]
                    ]
                ], 422);
            }
        }
        else{
            if (($data['no_of_shop']) > $buildingData->number_shop) {

                $remaining = $buildingData->number_shop - $data['no_of_shop'];

                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => [
                        'no_of_shop' => [
                            "Only $remaining shops can be added"
                        ]
                    ]
                ], 422);
            }
        }
        // Prepare point data
        $pointData = [
            'point_gisid' => $data['point_gisid'],
            'building_data_id' => $buildingData->id,
            'assessment' => $data['assessment'] ?? null,
            'old_assessment' => $data['old_assessment'] ?? null,
            'owner_name' => $data['owner_name'] ?? null,
            'present_owner_name' => $data['present_owner_name'] ?? null,
            'floor' => $data['floor'] ?? null,
            'old_door_no' => $data['old_door_no'] ?? null,
            'new_door_no' => $data['new_door_no'] ?? null,
            'bill_usage' => $data['bill_usage'] ?? null,
            'eb' => $data['eb'] ?? null,
            'number_persons' => $data['number_persons'] ?? 0,
            'water_tax' => $data['water_tax'] ?? null,
            'old_water_tax' => $data['old_water_tax'] ?? null,
            'professional_tax' => $data['professional_tax'] ?? null,
            'gst' => $data['gst'] ?? null,
            'trade_income' => $data['trade_income'] ?? null,
            'aadhar_no' => $data['aadhar_no'] ?? null,
            'ration_no' => $data['ration_no'] ?? null,
            'phone_number' => $data['phone_number'] ?? null,
            'qc_area' => $data['qc_area'] ?? null,
            'qc_usage' => $data['qc_usage'] ?? null,
            'qc_name' => $data['qc_name'] ?? null,
            'qc_remarks' => $data['qc_remarks'] ?? null,
            'establishment_remarks' => $data['establishment_remarks'] ?? null,
            'remarks' => $data['remarks'] ?? null,
            'worker_name' => $teamMember->user->id . '-' . $teamMember->user->name,
            'assessment_type' => $data['type'] ?? 'OLD',
            'updated_at' => now(),
        ];

        try {
            DB::beginTransaction();
            // Insert new point data
            $pointData['created_at'] = now();
            $pointId = DB::table($pointDataTableName)->insertGetId($pointData);
            $message = 'Point data saved successfully';
            // Handle shop details
            $totalShops = (int)($data['total_shops'] ?? 0);
            // Get existing shop IDs for this point
            $existingShopIds = DB::table($shopDataTableName)
                ->where('point_data_id', $pointId)
                ->pluck('id')
                ->toArray();

            $newShopIds = [];

            // Process each shop
            for ($i = 1; $i <= $totalShops; $i++) {
                $shopData = [
                    'point_data_id' => $pointId,
                    'shop_floor' => $data["shop_floor_{$i}"] ?? null,
                    'shop_name' => $data["shop_name_{$i}"] ?? null,
                    'shop_owner_name' => $data["shop_owner_name_{$i}"] ?? null,
                    'shop_category' => $data["shop_category_{$i}"] ?? null,
                    'shop_mobile' => $data["shop_mobile_{$i}"] ?? null,
                    'license' => $data["license_{$i}"] ?? null,
                    'number_of_employee' => $data["number_of_employee_{$i}"] ?? null,
                    'updated_at' => now(),
                ];

                // Check if shop already exists (by matching data)
                $existingShop = DB::table($shopDataTableName)
                    ->where('point_data_id', $pointId)
                    ->where('shop_floor', $shopData['shop_floor'])
                    ->where('shop_name', $shopData['shop_name'])
                    ->first();

                if ($existingShop) {
                    // Update existing shop
                    DB::table($shopDataTableName)
                        ->where('id', $existingShop->id)
                        ->update($shopData);
                    $newShopIds[] = $existingShop->id;
                } else {
                    // Insert new shop
                    $shopData['created_at'] = now();
                    $shopId = DB::table($shopDataTableName)->insertGetId($shopData);
                    $newShopIds[] = $shopId;
                }
            }

            // Delete shops that are no longer present
            $shopsToDelete = array_diff($existingShopIds, $newShopIds);
            if (!empty($shopsToDelete)) {
                DB::table($shopDataTableName)
                    ->whereIn('id', $shopsToDelete)
                    ->delete();
            }

            DB::commit();

            // Fetch updated data to return
            $updatedPointData = DB::table($pointDataTableName)
                ->where('point_gisid', $data['point_gisid'])
                ->first();

            $updatedShops = DB::table($shopDataTableName)
                ->where('point_data_id', $pointId)
                ->get();

            return response()->json([
                'success' => true,
                'message' => $message,
                'pointData' => $updatedPointData,
                'shops' => $updatedShops,
                'pointDatas' => DB::table($pointDataTableName)->get(),
                'points' => $this->getPointsData($ward) // You'll need to implement this method
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ], 500);
        }
    }

    // Helper method to get points data
    private function getPointsData($ward)
    {
        $zone = strtolower(trim($ward->zone));
        $wardNo = (int)$ward->ward_no;
        $corp = (int)$ward->corporation_id;
        $pointsTableName = "point_{$corp}_{$zone}_{$wardNo}";

        return DB::table($pointsTableName)->get();
    }

    public function searchPointData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'gisid' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $userId = Auth::id();

        $teamMember = TeamMember::with(['team.ward'])
            ->where('user_id', $userId)
            ->first();

        if (!$teamMember) {
            return response()->json([
                'success' => false,
                'message' => 'You are not assigned to any team.',
            ], 403);
        }

        $ward = $teamMember->team->ward;
        $zone = strtolower(trim($ward->zone));
        $wardNo = (int)$ward->ward_no;
        $corp = (int)$ward->corporation_id;

        $table = "pointdata_{$corp}_{$zone}_{$wardNo}";

        $gisid = $request->gisid;

        $pointData = DB::table($table)
            ->where('point_gisid', $gisid)
            ->first();

        if (!$pointData) {
            $pointassessment = DB::table($table)
                ->where('assessment', 'like', '%' . $gisid . '%')
                ->first();

            if (!$pointassessment) {
                return response()->json([
                    'success' => false,
                    'message' => 'No data found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Found via assessment',
                'data' => $gisid
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Found via GIS ID',
            'data' => $gisid
        ]);
    }
    public function getPointData(Request $request, $gisid)
    {
        $userId = Auth::id();

        $teamMember = TeamMember::with(['team.ward', 'user'])
            ->where('user_id', $userId)
            ->first();

        if (!$teamMember) {
            return redirect()->back()->with('error', 'You are not assigned to any team.');
        }

        $ward = $teamMember->team->ward;
        $zone = strtolower(trim($ward->zone));
        $wardNo = (int)$ward->ward_no;
        $corp = (int)$ward->corporation_id;

        $pointTable = "pointdata_{$corp}_{$zone}_{$wardNo}";
        $shopTable = "shopdata_{$corp}_{$zone}_{$wardNo}";

        // Get ALL points with this GIS ID (NOT just first)
        $points = DB::table($pointTable)
            ->where('point_gisid', $gisid)
            ->get();

        // If not found by GIS ID, try assessment search
        if ($points->isEmpty()) {
            $points = DB::table($pointTable)
                ->where('assessment', 'like', '%' . $gisid . '%')
                ->get();
        }

        if ($points->isEmpty()) {
            return redirect()->back()->with('error', 'No data found for GIS ID: ' . $gisid);
        }

        // Get shops for each point
        foreach ($points as $point) {
            $point->shops = DB::table($shopTable)
                ->where('point_data_id', $point->id)
                ->get();
        }

        $surveyor = [
            'name' => $teamMember->user->name,
            'id' => $teamMember->user->id
        ];

        return view('surveyor.point-data-edit', [
            'pointData' => $points,  // This is a collection, will be converted to array by @json
            'surveyor' => $surveyor,
            'gisid' => $gisid,
            'corp' => $corp,
            'zone' => $zone,
            'wardNo' => $wardNo
        ]);
    }
    public function updatePointRecord(Request $request)
    {
        try {
            $corp = $request->corp;
            $zone = $request->zone;
            $wardNo = $request->ward_no;

            if ($request->type === 'point') {
                $table = "pointdata_{$corp}_{$zone}_{$wardNo}";
                DB::table($table)
                    ->where('id', $request->id)
                    ->update($request->data);
            } else {
                $table = "shopdata_{$corp}_{$zone}_{$wardNo}";
                DB::table($table)
                    ->where('id', $request->id)
                    ->update($request->data);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function addShopRecord(Request $request)
    {
        try {
            $corp = $request->corp;
            $zone = $request->zone;
            $wardNo = $request->ward_no;
            $table = "shopdata_{$corp}_{$zone}_{$wardNo}";

            $shopData = $request->shop_data;
            $shopData['created_at'] = now();
            $shopData['updated_at'] = now();

            $id = DB::table($table)->insertGetId($shopData);

            return response()->json(['success' => true, 'id' => $id]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function deleteShopRecord(Request $request)
    {
        try {
            $corp = $request->corp;
            $zone = $request->zone;
            $wardNo = $request->ward_no;
            $table = "shopdata_{$corp}_{$zone}_{$wardNo}";

            DB::table($table)->where('id', $request->shop_id)->delete();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function bulkUpdatePoints(Request $request)
    {
        try {
            $corp = $request->corp;
            $zone = $request->zone;
            $wardNo = $request->ward_no;
            $table = "pointdata_{$corp}_{$zone}_{$wardNo}";

            foreach ($request->points as $point) {
                DB::table($table)
                    ->where('id', $point['id'])
                    ->update($point['data']);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}

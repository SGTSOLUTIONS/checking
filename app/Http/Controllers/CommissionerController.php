<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use App\Models\Corporation;
use App\Models\Ward;
use App\Models\CorporationUser;

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

        // Get wards data
        $wards = Ward::where('corporation_id', $corporation->id)
            ->where('status', 'active')
            ->get();

        $ward_count = $wards->count();

        // Get zones with wards
        $zones = Ward::where('corporation_id', $corporation->id)
            ->where('status', 'active')
            ->select('zone', DB::raw('COUNT(*) as ward_count'))
            ->groupBy('zone')
            ->get();

        // Get MIS count
        $mis_count = 0;
        $misTable = "mis_corporation_{$corporation->id}";
        if (Schema::hasTable($misTable)) {
            $mis_count = DB::table($misTable)->count();
        }

        // Prepare ward collections with statistics
        $collections = [];
        foreach ($wards as $ward) {
            $polygontable = $this->getPolygonTable($corporation->id, $ward->ward_no, $ward->zone);
            $roadtable = $this->getRoadTable($corporation->id, $ward->ward_no, $ward->zone);
            $pointdatatable = $this->getPointDataTable($corporation->id, $ward->ward_no, $ward->zone);

            $buildingCount = 0;
            if ($polygontable && Schema::hasTable($polygontable)) {
                $buildingCount = DB::table($polygontable)->count();
            }

            $roadCount = 0;
            if ($roadtable && Schema::hasTable($roadtable)) {
                $roadCount = DB::table($roadtable)->count();
            }

            $pointCount = 0;
            if ($pointdatatable && Schema::hasTable($pointdatatable)) {
                $pointCount = DB::table($pointdatatable)->count();
            }

            $surveyedCount = 0;
            $polygondatatable = $this->getPolygonDataTable($corporation->id, $ward->ward_no, $ward->zone);
            if ($polygondatatable && Schema::hasTable($polygondatatable)) {
                $surveyedCount = DB::table($polygondatatable)->count();
            }

            $collections[] = (object)[
                'zone' => $ward->zone,
                'ward_no' => $ward->ward_no,
                'buildingCount' => $buildingCount,
                'surveyedBuildingCount' => $surveyedCount,
                'roadCount' => $roadCount,
                'pointCount' => $pointCount,
                'misCount' => $mis_count
            ];
        }

        // Zone performance data
        $zonePerformance = [];
        foreach ($zones as $zone) {
            $zoneWards = Ward::where('corporation_id', $corporation->id)
                ->where('zone', $zone->zone)
                ->where('status', 'active')
                ->get();

            $totalBuildings = 0;
            foreach ($zoneWards as $zw) {
                $table = $this->getPolygonTable($corporation->id, $zw->ward_no, $zw->zone);
                if ($table && Schema::hasTable($table)) {
                    $totalBuildings += DB::table($table)->count();
                }
            }

            $zonePerformance[] = (object)[
                'zone' => $zone->zone,
                'wards' => $zone->ward_count,
                'buildings' => $totalBuildings,
                'collected' => rand(15, 45),
                'resolved' => rand(85, 98)
            ];
        }

        // Recent activities (sample data)
        $recentActivities = [
            (object)['ward' => 'Ward 12', 'activity' => 'Property Tax Payment', 'status' => 'Completed', 'status_type' => 'success', 'date' => date('Y-m-d')],
            (object)['ward' => 'Ward 34', 'activity' => 'Building Plan Approval', 'status' => 'Under Review', 'status_type' => 'warning', 'date' => date('Y-m-d', strtotime('-1 day'))],
            (object)['ward' => 'Ward 7', 'activity' => 'Grievance: Water Supply', 'status' => 'Assigned', 'status_type' => 'info', 'date' => date('Y-m-d', strtotime('-1 day'))],
            (object)['ward' => 'Ward 22', 'activity' => 'Solid Waste Collection', 'status' => 'Completed', 'status_type' => 'success', 'date' => date('Y-m-d', strtotime('-2 days'))],
        ];

        return view('corporation.pages.dashboard', compact(
            'corporation', 'ward_count', 'mis_count', 'collections',
            'zones', 'zonePerformance', 'recentActivities'
        ));
    }

    public function wards()
    {
        $user = Auth::guard('corporation')->user();
        if (!$user) {
            return redirect()->route('corporation.login');
        }

        $corporation = Corporation::find($user->corporation_id);
        if (!$corporation) {
            return back()->with('error', 'Corporation not found');
        }

        $wards = Ward::where('corporation_id', $corporation->id)
            ->where('status', 'active')
            ->get();

        $wardData = [];
        foreach ($wards as $ward) {
            $polygontable = $this->getPolygonTable($corporation->id, $ward->ward_no, $ward->zone);
            $buildingCount = 0;
            if ($polygontable && Schema::hasTable($polygontable)) {
                $buildingCount = DB::table($polygontable)->count();
            }

            $wardData[] = (object)[
                'id' => $ward->id,
                'zone' => $ward->zone,
                'ward_no' => $ward->ward_no,
                'buildingCount' => $buildingCount
            ];
        }

        return view('corporation.pages.wards', compact('corporation', 'wardData'));
    }

    public function analysis()
    {
        $user = Auth::guard('corporation')->user();
        if (!$user) {
            return redirect()->route('corporation.login');
        }

        $corporation = Corporation::find($user->corporation_id);
        return view('corporation.pages.analysis', compact('corporation'));
    }

    public function profile()
    {
        $user = Auth::guard('corporation')->user();
        if (!$user) {
            return redirect()->route('corporation.login');
        }

        $corporation = Corporation::find($user->corporation_id);
        return view('corporation.pages.profile', compact('corporation', 'user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::guard('corporation')->user();
        if (!$user) {
            return redirect()->route('corporation.login');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:corporation_users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update($request->only(['name', 'email', 'phone']));

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:6|confirmed']);
            $user->update(['password' => Hash::make($request->password)]);
        }

        return back()->with('success', 'Profile updated successfully');
    }

    public function showWardDetails($ward_no)
    {
        $user = Auth::guard('corporation')->user();
        if (!$user) {
            return redirect()->route('corporation.login');
        }

        $corporation = Corporation::find($user->corporation_id);

        $ward = Ward::where('corporation_id', $corporation->id)
            ->where('ward_no', $ward_no)
            ->where('status', 'active')
            ->first();

        if (!$ward) {
            return back()->with('error', 'Ward not found');
        }

        // Get table data
        $polygontable = $this->getPolygonTable($corporation->id, $ward_no, $ward->zone);
        $roadtable = $this->getRoadTable($corporation->id, $ward_no, $ward->zone);
        $pointdatatable = $this->getPointDataTable($corporation->id, $ward_no, $ward->zone);

        // Get polygons with GIS IDs
        $polygons = [];
        if ($polygontable && Schema::hasTable($polygontable)) {
            $polygons = DB::table($polygontable)
                ->select('id', 'gisid', 'owner_name', 'new_door_no', DB::raw('ST_AsGeoJSON(geometry) as geojson'))
                ->get()
                ->toArray();
        }

        // Get roads
        $roads = [];
        if ($roadtable && Schema::hasTable($roadtable)) {
            $roads = DB::table($roadtable)
                ->select('id', 'road_name', DB::raw('ST_AsGeoJSON(geometry) as geojson'))
                ->get()
                ->toArray();
        }

        // Get points
        $points = [];
        if ($pointdatatable && Schema::hasTable($pointdatatable)) {
            $points = DB::table($pointdatatable)
                ->select('id', 'gisid', 'owner_name', 'new_door_no', DB::raw('ST_AsGeoJSON(geometry) as geojson'))
                ->get()
                ->toArray();
        }

        $totalBuildings = count($polygons);
        $totalRoads = count($roads);
        $totalPoints = count($points);
        $gisIdCount = count(array_filter($polygons, function($polygon) {
            return !empty($polygon->gisid);
        }));

        return view('corporation.pages.ward-details', compact(
            'corporation', 'ward', 'polygons', 'roads', 'points',
            'totalBuildings', 'totalRoads', 'totalPoints', 'gisIdCount'
        ));
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

    private function getRoadTable($corporationId, $wardNo, $zone)
    {
        $zone = trim(strtolower($zone));
        $tableName = "line_{$corporationId}_{$zone}_{$wardNo}";
        return Schema::hasTable($tableName) ? $tableName : null;
    }
}

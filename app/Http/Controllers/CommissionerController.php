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
                $roadtable = $this->getRoadTable($corporation->id, $wardlist->ward_no, $wardlist->zone);

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

    /**
     * Display ward details with map.
     */
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
                ->select('id', 'gisid', 'owner_name', DB::raw('ST_AsGeoJSON(geometry) as geojson'))
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

        // Get zones and wards for sidebar
        $zonesWithWards = [];
        $wards_per_zones = Ward::where('corporation_id', $corporation->id)
            ->where('status', 'active')
            ->select('zone', DB::raw('count(*) as total'))
            ->groupBy('zone')
            ->get();

        foreach ($wards_per_zones as $wards_per_zone) {
            $wardlists = Ward::where('zone', $wards_per_zone->zone)
                ->where('status', 'active')
                ->get();

            $zoneData = [
                'zone' => $wards_per_zone->zone,
                'wards' => []
            ];

            foreach ($wardlists as $w) {
                $polyTable = $this->getPolygonTable($corporation->id, $w->ward_no, $w->zone);
                $buildingCount = ($polyTable && Schema::hasTable($polyTable)) ? DB::table($polyTable)->count() : 0;

                $zoneData['wards'][] = [
                    'ward_no' => $w->ward_no,
                    'buildingCount' => $buildingCount
                ];
            }

            $zonesWithWards[] = $zoneData;
        }

        // Calculate statistics
        $totalBuildings = count($polygons);
        $totalRoads = count($roads);
        $totalPoints = count($points);
        $gisIdCount = count(array_filter($polygons, function ($polygon) {
            return !empty($polygon->gisid);
        }));
return response()->json( $corporation);
        return view('corporation.ward-details', [
            'corporation' => $corporation,
            'ward' => $ward,
            'polygons' => $polygons,
            'roads' => $roads,
            'points' => $points,
            'zonesWithWards' => $zonesWithWards,
            'totalBuildings' => $totalBuildings,
            'totalRoads' => $totalRoads,
            'totalPoints' => $totalPoints,
            'gisIdCount' => $gisIdCount
        ]);
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

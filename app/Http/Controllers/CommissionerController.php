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

        /*
        |--------------------------------------------------------------------------
        | WARDS
        |--------------------------------------------------------------------------
        */

        $wards = Ward::where('corporation_id', $corporation->id)
            ->where('status', 'active')
            ->get();

        $ward_count = $wards->count();

        $wards_per_zones = Ward::where('corporation_id', $corporation->id)
            ->where('status', 'active')
            ->select('zone', DB::raw('count(*) as total'))
            ->groupBy('zone')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | MIS MAIN DATA
        |--------------------------------------------------------------------------
        */

        $mis = [];
        $mis_count = 0;

        $misTable = "mis_corporation_{$corporation->id}";

        if (Schema::hasTable($misTable)) {
            $mis = DB::table($misTable)->get();
            $mis_count = DB::table($misTable)->count();
        }

        /*
        |--------------------------------------------------------------------------
        | COLLECTIONS
        |--------------------------------------------------------------------------
        */

        $collections = [];
        $zonesWithWards = [];

        foreach ($wards_per_zones as $wards_per_zone) {
            $wardlists = Ward::where('zone', $wards_per_zone->zone)->get();

            $zoneData = [
                'zone' => $wards_per_zone->zone,
                'wards' => []
            ];

            foreach ($wardlists as $wardlist) {
                /*
                |--------------------------------------------------------------------------
                | TABLE NAMES
                |--------------------------------------------------------------------------
                */

                $pointdatatable = $this->getpointdatatable(
                    $wardlist->corporation_id,
                    $wardlist->ward_no,
                    $wardlist->zone
                );

                $polygondatatable = $this->getpolygondatatable(
                    $wardlist->corporation_id,
                    $wardlist->ward_no,
                    $wardlist->zone
                );

                $polygontable = $this->getpolygontable(
                    $wardlist->corporation_id,
                    $wardlist->ward_no,
                    $wardlist->zone
                );

                $roadtable = $this->getroadtable(
                    $wardlist->corporation_id,
                    $wardlist->ward_no,
                    $wardlist->zone
                );

                /*
                |--------------------------------------------------------------------------
                | MIS DATA
                |--------------------------------------------------------------------------
                */

                $misData = [];
                $misCount = 0;

                $misWardTable = "mis_corporation_{$wardlist->corporation_id}";

                if (Schema::hasTable($misWardTable)) {
                    $misData = DB::table($misWardTable)
                        ->where('ward_no', $wardlist->ward_no)
                        ->get();

                    $misCount = DB::table($misWardTable)
                        ->where('ward_no', $wardlist->ward_no)
                        ->count();
                }

                /*
                |--------------------------------------------------------------------------
                | TOTAL BUILDINGS
                |--------------------------------------------------------------------------
                */

                $buildingCount = 0;
                $polygonData = [];

                if ($polygontable && Schema::hasTable($polygontable)) {
                    $buildingCount = DB::table($polygontable)->count();
                    $polygonData = DB::table($polygontable)->get();
                }

                /*
                |--------------------------------------------------------------------------
                | SURVEYED BUILDINGS
                |--------------------------------------------------------------------------
                */

                $surveyedBuildingCount = 0;
                $surveyedBuildingData = [];

                if ($polygondatatable && Schema::hasTable($polygondatatable)) {
                    $surveyedBuildingCount = DB::table($polygondatatable)->count();
                    $surveyedBuildingData = DB::table($polygondatatable)->get();
                }

                /*
                |--------------------------------------------------------------------------
                | POINT DATA
                |--------------------------------------------------------------------------
                */

                $pointCount = 0;
                $pointData = [];

                if ($pointdatatable && Schema::hasTable($pointdatatable)) {
                    $pointCount = DB::table($pointdatatable)->count();
                    $pointData = DB::table($pointdatatable)->get();
                }

                /*
                |--------------------------------------------------------------------------
                | ROAD DATA
                |--------------------------------------------------------------------------
                */

                $roadCount = 0;
                $roadData = [];

                if ($roadtable && Schema::hasTable($roadtable)) {
                    $roadCount = DB::table($roadtable)->count();
                    $roadData = DB::table($roadtable)->get();
                }

                /*
                |--------------------------------------------------------------------------
                | FINAL DATA
                |--------------------------------------------------------------------------
                */

                $data = [
                    "zone"                     => $wardlist->zone,
                    "ward_no"                  => $wardlist->ward_no,
                    "pointdatatable"           => $pointdatatable,
                    "polygondatatable"         => $polygondatatable,
                    "polygontable"             => $polygontable,
                    "roadtable"                => $roadtable,
                    "buildingCount"            => $buildingCount,
                    "polygonData"              => $polygonData,
                    "surveyedBuildingCount"    => $surveyedBuildingCount,
                    "surveyedBuildingData"     => $surveyedBuildingData,
                    "pointCount"               => $pointCount,
                    "pointData"                => $pointData,
                    "roadCount"                => $roadCount,
                    "roadData"                 => $roadData,
                    "misCount"                 => $misCount,
                    "misData"                  => $misData,
                ];

                $collections[] = $data;

                // Add to zone structure for sidebar
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
     *
     * @param Request $request
     * @param int $ward_no
     * @return \Illuminate\View\View
     */
    public function showWardDetails(Request $request, $ward_no)
    {
        $user = Auth::guard('corporation')->user();

        if (!$user) {
            return redirect()->route('corporation.login');
        }

        $corporation = Corporation::find($user->corporation_id);

        // Get ward details
        $ward = Ward::where('corporation_id', $corporation->id)
            ->where('ward_no', $ward_no)
            ->where('status', 'active')
            ->first();

        if (!$ward) {
            return back()->with('error', 'Ward not found');
        }

        // Get table names
        $polygontable = $this->getpolygontable($corporation->id, $ward_no, $ward->zone);
        $roadtable = $this->getroadtable($corporation->id, $ward_no, $ward->zone);
        $pointdatatable = $this->getpointdatatable($corporation->id, $ward_no, $ward->zone);

        // Get polygon data with GIS IDs and coordinates
        $polygons = [];
        if ($polygontable && Schema::hasTable($polygontable)) {
            $polygons = DB::table($polygontable)
                ->select('id', 'gisid', 'owner_name', 'geometry', DB::raw('ST_AsGeoJSON(geometry) as geojson'))
                ->get();
        }

        // Get road data
        $roads = [];
        if ($roadtable && Schema::hasTable($roadtable)) {
            $roads = DB::table($roadtable)
                ->select('id', 'road_name', 'geometry', DB::raw('ST_AsGeoJSON(geometry) as geojson'))
                ->get();
        }

        // Get point data
        $points = [];
        if ($pointdatatable && Schema::hasTable($pointdatatable)) {
            $points = DB::table($pointdatatable)
                ->select('id', 'gisid', 'owner_name', 'new_door_no', 'geometry', DB::raw('ST_AsGeoJSON(geometry) as geojson'))
                ->get();
        }

        // Get all zones and wards for sidebar
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
                // Get counts for each ward
                $polyTable = $this->getpolygontable($corporation->id, $w->ward_no, $w->zone);
                $buildingCount = ($polyTable && Schema::hasTable($polyTable)) ? DB::table($polyTable)->count() : 0;

                $zoneData['wards'][] = [
                    'ward_no' => $w->ward_no,
                    'buildingCount' => $buildingCount
                ];
            }

            $zonesWithWards[] = $zoneData;
        }

        return view('corporation.ward-details', [
            'corporation' => $corporation,
            'ward' => $ward,
            'polygons' => $polygons,
            'roads' => $roads,
            'points' => $points,
            'zonesWithWards' => $zonesWithWards
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | POINT TABLE
    |--------------------------------------------------------------------------
    */

    private function getpointdatatable($corporationId, $wardNo, $zone)
    {
        $zone = trim(strtolower($zone));
        $tableName = "pointdata_{$corporationId}_{$zone}_{$wardNo}";

        if (Schema::hasTable($tableName)) {
            return $tableName;
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | POLYGON DATA TABLE
    |--------------------------------------------------------------------------
    */

    private function getpolygondatatable($corporationId, $wardNo, $zone)
    {
        $zone = trim(strtolower($zone));
        $tableName = "polygondata_{$corporationId}_{$zone}_{$wardNo}";

        if (Schema::hasTable($tableName)) {
            return $tableName;
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | POLYGON TABLE
    |--------------------------------------------------------------------------
    */

    private function getpolygontable($corporationId, $wardNo, $zone)
    {
        $zone = trim(strtolower($zone));
        $tableName = "polygon_{$corporationId}_{$zone}_{$wardNo}";

        if (Schema::hasTable($tableName)) {
            return $tableName;
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | ROAD TABLE
    |--------------------------------------------------------------------------
    */

    private function getroadtable($corporationId, $wardNo, $zone)
    {
        $zone = trim(strtolower($zone));
        $tableName = "line_{$corporationId}_{$zone}_{$wardNo}";

        if (Schema::hasTable($tableName)) {
            return $tableName;
        }

        return null;
    }
}

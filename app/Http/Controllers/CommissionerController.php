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

        $ward_count = Ward::where('corporation_id', $corporation->id)
            ->where('status', 'active')
            ->count();

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

        foreach ($wards_per_zones as $wards_per_zone) {

            $wardlists = Ward::where('zone', $wards_per_zone->zone)->get();

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
                | polygon_* => Total buildings in ward
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
                | polygondatatable_* => Surveyed buildings
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
                | pointdata_* => Tax / bill / assessment entries
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
                | line_* => Road / line GIS data
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

                    // TABLE NAMES
                    "pointdatatable"           => $pointdatatable,
                    "polygondatatable"         => $polygondatatable,
                    "polygontable"             => $polygontable,
                    "roadtable"                => $roadtable,

                    // TOTAL BUILDINGS
                    "buildingCount"            => $buildingCount,
                    "polygonData"              => $polygonData,

                    // SURVEYED BUILDINGS
                    "surveyedBuildingCount"    => $surveyedBuildingCount,
                    "surveyedBuildingData"     => $surveyedBuildingData,

                    // POINT DATA
                    "pointCount"               => $pointCount,
                    "pointData"                => $pointData,

                    // ROAD DATA
                    "roadCount"                => $roadCount,
                    "roadData"                 => $roadData,

                    // MIS
                    "misCount"                 => $misCount,
                    "misData"                  => $misData,
                ];

                $collections[] = $data;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        return response()->json([

            "corporation"      => $corporation,
            "ward_count"       => $ward_count,
            "mis_count"        => $mis_count,
            "collections"      => $collections

        ]);

        // Return view for web request
        return view('corporation.dashboard', compact('dashboardData'));
    }

    /*
    |--------------------------------------------------------------------------
    | POINT TABLE
    |--------------------------------------------------------------------------
    */

    private function getpointdatatable($corporationId, $wardNo, $zone)
    {
        $zone = strtolower($zone);

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
        $zone = strtolower($zone);

        $tableName = "polygondatatable_{$corporationId}_{$zone}_{$wardNo}";

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
        $zone = strtolower($zone);

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
        $zone = strtolower($zone);

        $tableName = "line_{$corporationId}_{$zone}_{$wardNo}";

        if (Schema::hasTable($tableName)) {
            return $tableName;
        }

        return null;
    }
}

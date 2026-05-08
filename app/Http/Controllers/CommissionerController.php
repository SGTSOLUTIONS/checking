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
        $wateTaxTableName = "watertax_corporation_{$corp}";

        // Get data
        $polygons = DB::table($polygonsTableName)->get();
        $lines = DB::table($linesTableName)->get();
        $points = DB::table($pointsTableName)->get();
        $polygonDatas = DB::table($polygonDataTableName)->get();
        $pointDatas = DB::table($pointDataTableName)->get();
        $misData = DB::table($misTableName . ' as mis')
            ->join($wateTaxTableName . ' as wt', 'mis.assessment', '=', 'wt.assessment')
            ->select(
                'mis.*',
                'wt.watertax_no',
                'wt.old_watertax_no'
            )
            ->get();

        // Get unique road names from misData
        $uniqueRoadNames = DB::table($misTableName)
            ->select('road_name')
            ->whereNotNull('road_name')
            ->where('road_name', '!=', '')
            ->distinct()
            ->orderBy('road_name')
            ->pluck('road_name');

        return view('corporation.ward-map', compact(
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

    private function getRoadTable($corporationId, $wardNo, $zone)
    {
        $zone = trim(strtolower($zone));
        $tableName = "line_{$corporationId}_{$zone}_{$wardNo}";
        return Schema::hasTable($tableName) ? $tableName : null;
    }
}

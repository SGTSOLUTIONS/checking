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
    try {
        // Get authenticated user
        $userId = Auth::user();

        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        // Get ward details
        $warddetail = Ward::where('corporation_id', $userId->corporation_id)
            ->where('ward_no', $ward_no)
            ->first();

        if (!$warddetail) {
            return redirect()->back()->with('error', 'Ward not found.');
        }

        // Prepare table names
        $zone = strtolower(trim($warddetail->zone));
        $wardNo = (int)$warddetail->ward_no;
        $corp = (int)$warddetail->corporation_id;

        $tableNames = [
            'polygons' => "polygon_{$corp}_{$zone}_{$wardNo}",
            'polygonData' => "polygondata_{$corp}_{$zone}_{$wardNo}",
            'points' => "point_{$corp}_{$zone}_{$wardNo}",
            'pointData' => "pointdata_{$corp}_{$zone}_{$wardNo}",
            'lines' => "line_{$corp}_{$zone}_{$wardNo}",
            'mis' => "mis_corporation_{$corp}",
            'waterTax' => "watertax_corporation_{$corp}"
        ];

        // Initialize data arrays with default values
        $polygons = collect();
        $lines = collect();
        $points = collect();
        $polygonDatas = collect();
        $pointDatas = collect();
        $misData = collect();
        $uniqueRoadNames = collect();

        // Check if tables exist and fetch data
        $schema = DB::connection()->getSchemaBuilder();

        // Fetch polygons
        if ($schema->hasTable($tableNames['polygons'])) {
            $polygons = DB::table($tableNames['polygons'])->get();
        } else {
            \Log::warning("Table not found: {$tableNames['polygons']}");
        }

        // Fetch lines
        if ($schema->hasTable($tableNames['lines'])) {
            $lines = DB::table($tableNames['lines'])->get();
        } else {
            \Log::warning("Table not found: {$tableNames['lines']}");
        }

        // Fetch points
        if ($schema->hasTable($tableNames['points'])) {
            $points = DB::table($tableNames['points'])->get();
        } else {
            \Log::warning("Table not found: {$tableNames['points']}");
        }

        // Fetch polygon data
        if ($schema->hasTable($tableNames['polygonData'])) {
            $polygonDatas = DB::table($tableNames['polygonData'])->get();
        } else {
            \Log::warning("Table not found: {$tableNames['polygonData']}");
        }

        // Fetch point data
        if ($schema->hasTable($tableNames['pointData'])) {
            $pointDatas = DB::table($tableNames['pointData'])->get();
        } else {
            \Log::warning("Table not found: {$tableNames['pointData']}");
        }

        // Fetch MIS and Water Tax data with proper join
        if ($schema->hasTable($tableNames['mis']) && $schema->hasTable($tableNames['waterTax'])) {
            $misData = DB::table($tableNames['mis'] . ' as mis')
                ->leftJoin($tableNames['waterTax'] . ' as wt', 'mis.assessment', '=', 'wt.assessment')
                ->select(
                    'mis.*',
                    'wt.watertax_no',
                    'wt.old_watertax_no'
                )
                ->get();

            // Get unique road names
            $uniqueRoadNames = DB::table($tableNames['mis'])
                ->select('road_name')
                ->whereNotNull('road_name')
                ->where('road_name', '!=', '')
                ->distinct()
                ->orderBy('road_name')
                ->pluck('road_name');
        } else {
            \Log::warning("MIS or WaterTax table not found: {$tableNames['mis']} or {$tableNames['waterTax']}");
        }

        // Transform coordinates from JSON strings to arrays
        $polygons = $this->transformCoordinates($polygons, 'coordinates');
        $lines = $this->transformCoordinates($lines, 'coordinates');
        $points = $this->transformCoordinates($points, 'coordinates');

        $ward = $warddetail;

        return view('corporation.ward-map', compact(
            'ward',
            'polygons',
            'points',
            'lines',
            'polygonDatas',
            'pointDatas',
            'misData',
            'uniqueRoadNames'
        ));

    } catch (\Exception $e) {
        \Log::error("Error in mapView: " . $e->getMessage());
        \Log::error($e->getTraceAsString());

        return redirect()->back()->with('error', 'An error occurred while loading the map data. Please try again later.');
    }
}

/**
 * Transform coordinate strings to arrays for JSON encoding
 */
private function transformCoordinates($collection, $field)
{
    return $collection->map(function ($item) use ($field) {
        if (isset($item->$field) && is_string($item->$field)) {
            try {
                $item->$field = json_decode($item->$field, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    \Log::warning("Failed to decode JSON for field: {$field}", ['error' => json_last_error_msg()]);
                    $item->$field = null;
                }
            } catch (\Exception $e) {
                \Log::error("Error decoding coordinates: " . $e->getMessage());
                $item->$field = null;
            }
        }
        return $item;
    });
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

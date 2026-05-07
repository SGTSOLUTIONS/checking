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

    /**
     * Display ward map view with all GIS features.
     */
    public function mapView($ward_no)
    {
        $user = Auth::guard('corporation')->user();

        if (!$user) {
            return redirect()->route('corporation.login');
        }

        $corporation = Corporation::find($user->corporation_id);

        if (!$corporation) {
            return back()->with('error', 'Corporation not found');
        }

        $ward = Ward::where('corporation_id', $corporation->id)
            ->where('ward_no', $ward_no)
            ->where('status', 'active')
            ->first();

        if (!$ward) {
            return back()->with('error', 'Ward not found');
        }

        $zone = strtolower(trim($ward->zone));
        $wardNo = (int)$ward->ward_no;
        $corpId = (int)$corporation->id;

        // Table names
        $polygonTable = "polygon_{$corpId}_{$zone}_{$wardNo}";
        $lineTable = "line_{$corpId}_{$zone}_{$wardNo}";
        $pointTable = "point_{$corpId}_{$zone}_{$wardNo}";
        $polygonDataTable = "polygondata_{$corpId}_{$zone}_{$wardNo}";
        $pointDataTable = "pointdata_{$corpId}_{$zone}_{$wardNo}";
        $misTable = "mis_corporation_{$corpId}";
        $waterTaxTable = "watertax_corporation_{$corpId}";

        // Get GIS data with geometry as GeoJSON
        $polygons = [];
        if (Schema::hasTable($polygonTable)) {
            $polygons = DB::table($polygonTable)
                ->select('id', 'gisid', DB::raw('ST_AsGeoJSON(geometry) as geojson'))
                ->get()
                ->toArray();
        }

        $lines = [];
        if (Schema::hasTable($lineTable)) {
            $lines = DB::table($lineTable)
                ->select('id', 'gisid', 'road_name', DB::raw('ST_AsGeoJSON(geometry) as geojson'))
                ->get()
                ->toArray();
        }

        $points = [];
        if (Schema::hasTable($pointTable)) {
            $points = DB::table($pointTable)
                ->select('id', 'gisid', DB::raw('ST_AsGeoJSON(geometry) as geojson'))
                ->get()
                ->toArray();
        }

        // Get building data (polygondata)
        $buildingData = [];
        if (Schema::hasTable($polygonDataTable)) {
            $buildingData = DB::table($polygonDataTable)->get()->toArray();
        }

        // Get point data
        $pointData = [];
        if (Schema::hasTable($pointDataTable)) {
            $pointData = DB::table($pointDataTable)->get()->toArray();
        }

        // Get MIS data with water tax info
        $misData = [];
        if (Schema::hasTable($misTable)) {
            $query = DB::table($misTable . ' as mis')
                ->select('mis.*');

            if (Schema::hasTable($waterTaxTable)) {
                $query->leftJoin($waterTaxTable . ' as wt', 'mis.assessment', '=', 'wt.assessment')
                    ->addSelect('wt.watertax_no', 'wt.old_watertax_no');
            }

            $misData = $query->get()->toArray();
        }

        // Get statistics for dashboard
        $totalBuildings = count($polygons);
        $totalRoads = count($lines);
        $totalPoints = count($points);
        $gisIdCount = count(array_filter($polygons, function($polygon) {
            return !empty($polygon->gisid);
        }));

        // Building type statistics
        $buildingTypes = [];
        $constructionTypes = [];
        $usageTypes = [];

        foreach ($buildingData as $building) {
            if (!empty($building->building_type)) {
                $buildingTypes[$building->building_type] = ($buildingTypes[$building->building_type] ?? 0) + 1;
            }
            if (!empty($building->construction_type)) {
                $constructionTypes[$building->construction_type] = ($constructionTypes[$building->construction_type] ?? 0) + 1;
            }
            if (!empty($building->building_usage)) {
                $usageTypes[$building->building_usage] = ($usageTypes[$building->building_usage] ?? 0) + 1;
            }
        }

        // Area variation (building size classification based on plot area or building footprint)
        $areaVariations = [
            'Small (< 500 sq ft)' => 0,
            'Medium (500-1000 sq ft)' => 0,
            'Large (1000-2000 sq ft)' => 0,
            'Very Large (> 2000 sq ft)' => 0,
            'Unknown' => 0
        ];

        foreach ($buildingData as $building) {
            $plotArea = $building->plot_area ?? ($building->building_area ?? 0);
            if (!empty($plotArea)) {
                if ($plotArea < 500) {
                    $areaVariations['Small (< 500 sq ft)']++;
                } elseif ($plotArea < 1000) {
                    $areaVariations['Medium (500-1000 sq ft)']++;
                } elseif ($plotArea < 2000) {
                    $areaVariations['Large (1000-2000 sq ft)']++;
                } else {
                    $areaVariations['Very Large (> 2000 sq ft)']++;
                }
            } else {
                $areaVariations['Unknown']++;
            }
        }

        // Get unique road names
        $uniqueRoadNames = [];
        if (Schema::hasTable($misTable)) {
            $uniqueRoadNames = DB::table($misTable)
                ->select('road_name')
                ->whereNotNull('road_name')
                ->where('road_name', '!=', '')
                ->distinct()
                ->orderBy('road_name')
                ->pluck('road_name')
                ->toArray();
        }

        // Get zone and ward list for sidebar
        $zonesWithWards = [];
        $wardsPerZones = Ward::where('corporation_id', $corporation->id)
            ->where('status', 'active')
            ->select('zone', DB::raw('count(*) as total'))
            ->groupBy('zone')
            ->get();

        foreach ($wardsPerZones as $wardsPerZone) {
            $wardLists = Ward::where('zone', $wardsPerZone->zone)
                ->where('status', 'active')
                ->orderBy('ward_no')
                ->get();

            $zoneData = [
                'zone' => $wardsPerZone->zone,
                'wards' => []
            ];

            foreach ($wardLists as $w) {
                $polyTable = $this->getPolygonTable($corporation->id, $w->ward_no, $w->zone);
                $buildingCount = ($polyTable && Schema::hasTable($polyTable)) ? DB::table($polyTable)->count() : 0;

                $zoneData['wards'][] = [
                    'ward_no' => $w->ward_no,
                    'buildingCount' => $buildingCount
                ];
            }

            $zonesWithWards[] = $zoneData;
        }

        return view('corporation.ward-map', [
            'corporation' => $corporation,
            'ward' => $ward,
            'polygons' => $polygons,
            'lines' => $lines,
            'points' => $points,
            'buildingData' => $buildingData,
            'pointData' => $pointData,
            'misData' => $misData,
            'totalBuildings' => $totalBuildings,
            'totalRoads' => $totalRoads,
            'totalPoints' => $totalPoints,
            'gisIdCount' => $gisIdCount,
            'buildingTypes' => $buildingTypes,
            'constructionTypes' => $constructionTypes,
            'usageTypes' => $usageTypes,
            'areaVariations' => $areaVariations,
            'uniqueRoadNames' => $uniqueRoadNames,
            'zonesWithWards' => $zonesWithWards
        ]);
    }

    /**
     * Search API for buildings by assessment number or GIS ID
     */
    public function searchBuilding(Request $request)
    {
        $user = Auth::guard('corporation')->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $corporation = Corporation::find($user->corporation_id);
        $searchTerm = $request->input('q');
        $type = $request->input('type', 'both'); // assessment, gisid, both

        if (empty($searchTerm)) {
            return response()->json(['results' => []]);
        }

        $results = [];

        // Search in MIS data
        $misTable = "mis_corporation_{$corporation->id}";
        if (Schema::hasTable($misTable)) {
            $query = DB::table($misTable);

            if ($type == 'assessment' || $type == 'both') {
                $query->where('assessment', 'LIKE', "%{$searchTerm}%");
            }

            if ($type == 'gisid' && $type != 'both') {
                $query->orWhere('gisid', 'LIKE', "%{$searchTerm}%");
            }

            $misResults = $query->limit(20)->get();

            foreach ($misResults as $item) {
                $results[] = [
                    'type' => 'assessment',
                    'value' => $item->assessment,
                    'label' => "Assessment: {$item->assessment}",
                    'data' => $item
                ];
            }
        }

        // Search in polygon data (GIS ID)
        foreach (['north', 'south', 'east', 'west', 'central'] as $zone) {
            $polygonDataTable = "polygondata_{$corporation->id}_{$zone}_*";
            // Note: You would need to iterate through actual ward tables
        }

        return response()->json(['results' => $results]);
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

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

        $wards = Ward::where('corporation_id', $corporation->id)
            ->where('status', 'active')
            ->get();

        $ward_count = $wards->count();

        $wards_per_zones = Ward::where('corporation_id', $corporation->id)
            ->where('status', 'active')
            ->select('zone', DB::raw('count(*) as total'))
            ->groupBy('zone')
            ->get();

        $mis_count = 0;
        $misTable = "mis_corporation_{$corporation->id}";
        if (Schema::hasTable($misTable)) {
            $mis_count = DB::table($misTable)->count();
        }

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
        $userId = Auth::user();
        $warddetail = Ward::where('corporation_id', $userId->corporation_id)->where('ward_no', $ward_no)->first();

        if (!$warddetail) {
            return back()->with('error', 'Ward not found');
        }

        $zone = strtolower(trim($warddetail->zone));
        $wardNo = (int)$warddetail->ward_no;
        $corp = (int)$warddetail->corporation_id;

        $polygonsTableName = "polygon_{$corp}_{$zone}_{$wardNo}";
        $polygonDataTableName = "polygondata_{$corp}_{$zone}_{$wardNo}";
        $pointsTableName = "point_{$corp}_{$zone}_{$wardNo}";
        $pointDataTableName = "pointdata_{$corp}_{$zone}_{$wardNo}";
        $linesTableName = "line_{$corp}_{$zone}_{$wardNo}";
        $misTableName = "mis_corporation_{$corp}";
        $waterTaxTableName = "watertax_corporation_{$corp}";
        $shopsTableName = "shops_corporation_{$corp}";

        $polygons = Schema::hasTable($polygonsTableName) ? DB::table($polygonsTableName)->get() : [];
        $lines = Schema::hasTable($linesTableName) ? DB::table($linesTableName)->get() : [];
        $points = Schema::hasTable($pointsTableName) ? DB::table($pointsTableName)->get() : [];
        $polygonDatas = Schema::hasTable($polygonDataTableName) ? DB::table($polygonDataTableName)->get() : [];
        $pointDatas = Schema::hasTable($pointDataTableName) ? DB::table($pointDataTableName)->get() : [];
        $shopDatas = Schema::hasTable($shopsTableName) ? DB::table($shopsTableName)->get() : [];

        $misData = [];
        if (Schema::hasTable($misTableName)) {
            $query = DB::table($misTableName . ' as mis');
            if (Schema::hasTable($waterTaxTableName)) {
                $waterTaxColumns = Schema::getColumnListing($waterTaxTableName);
                $selectColumns = ['mis.*'];
                if (in_array('watertax_no', $waterTaxColumns)) {
                    $selectColumns[] = 'wt.watertax_no';
                }
                if (in_array('old_watertax_no', $waterTaxColumns)) {
                    $selectColumns[] = 'wt.old_watertax_no';
                }
                if (in_array('water_tax', $waterTaxColumns)) {
                    $selectColumns[] = 'wt.water_tax as water_tax_amount';
                }
                if (in_array('water_tax_amount', $waterTaxColumns)) {
                    $selectColumns[] = 'wt.water_tax_amount';
                }
                $misData = $query->leftJoin($waterTaxTableName . ' as wt', 'mis.assessment', '=', 'wt.assessment')
                    ->select($selectColumns)
                    ->get();
            } else {
                $misData = $query->get();
            }
        }

        $uniqueRoadNames = [];
        if (Schema::hasTable($misTableName)) {
            $uniqueRoadNames = DB::table($misTableName)
                ->select('road_name')
                ->whereNotNull('road_name')
                ->where('road_name', '!=', '')
                ->distinct()
                ->orderBy('road_name')
                ->pluck('road_name');
        }

        $ward = $warddetail;

        foreach ($pointDatas as $pointData) {
            $pointData->table_name = $pointDataTableName;
        }

        return view('corporation.ward-map', compact(
            'ward',
            'polygons',
            'points',
            'lines',
            'polygonDatas',
            'pointDatas',
            'misData',
            'shopDatas',
            'uniqueRoadNames'
        ));
    }

    public function updateAssessment(Request $request)
    {
        try {
            $assessmentId = $request->id;
            $assessmentNo = $request->assessment_no;
            $squareFeet = $request->square_feet;
            $usage = $request->usage;
            $pointDataTable = $request->point_data_table;

            if (!$pointDataTable || !Schema::hasTable($pointDataTable)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Table not found: ' . $pointDataTable
                ]);
            }

            $columns = Schema::getColumnListing($pointDataTable);
            $updateData = [];

            if (in_array('qcsqfeet', $columns)) {
                $updateData['qcsqfeet'] = $squareFeet;
            } else if (in_array('sqfeet', $columns)) {
                $updateData['sqfeet'] = $squareFeet;
            }

            if (in_array('qcusage', $columns)) {
                $updateData['qcusage'] = $usage;
            } else if (in_array('usage', $columns)) {
                $updateData['usage'] = $usage;
            }

            if (in_array('updated_at', $columns)) {
                $updateData['updated_at'] = now();
            }

            if ($assessmentId && !empty($assessmentId)) {
                $updated = DB::table($pointDataTable)->where('id', $assessmentId)->update($updateData);
            } else if ($assessmentNo && !empty($assessmentNo)) {
                $updated = DB::table($pointDataTable)->where('assessment', $assessmentNo)->update($updateData);
            } else {
                return response()->json(['success' => false, 'message' => 'No ID or Assessment Number provided']);
            }

            if ($updated) {
                $updatedRecord = $assessmentId ?
                    DB::table($pointDataTable)->where('id', $assessmentId)->first() :
                    DB::table($pointDataTable)->where('assessment', $assessmentNo)->first();

                return response()->json([
                    'success' => true,
                    'message' => 'Assessment updated successfully',
                    'data' => [
                        'id' => $updatedRecord->id ?? null,
                        'assessment' => $updatedRecord->assessment ?? null,
                        'qcsqfeet' => $updatedRecord->qcsqfeet ?? null,
                        'qcusage' => $updatedRecord->qcusage ?? null,
                        'sqfeet' => $updatedRecord->sqfeet ?? null,
                        'usage' => $updatedRecord->usage ?? null,
                        'bill_usage' => $updatedRecord->bill_usage ?? null
                    ]
                ]);
            } else {
                return response()->json(['success' => false, 'message' => 'Assessment not found or no changes made']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function getAssessmentDetails(Request $request)
    {
        try {
            $assessmentNo = $request->assessment_no;
            $assessmentId = $request->assessment_id;
            $pointDataTable = $request->point_data_table;

            if (!$pointDataTable || !Schema::hasTable($pointDataTable)) {
                return response()->json(['success' => false, 'message' => 'Table not found']);
            }

            $query = DB::table($pointDataTable);
            if ($assessmentId && !empty($assessmentId)) {
                $query->where('id', $assessmentId);
            } else if ($assessmentNo && !empty($assessmentNo)) {
                $query->where('assessment', $assessmentNo);
            } else {
                return response()->json(['success' => false, 'message' => 'No ID or Assessment Number provided']);
            }

            $data = $query->first();

            if ($data) {
                $responseData = (array)$data;
                $responseData['sqfeet'] = $data->qcsqfeet ?? $data->sqfeet ?? null;
                $responseData['usage'] = $data->qcusage ?? $data->usage ?? null;
                return response()->json(['success' => true, 'data' => $responseData]);
            } else {
                return response()->json(['success' => false, 'message' => 'Assessment not found']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
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
}

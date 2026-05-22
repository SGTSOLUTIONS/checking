<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Corporation;
use App\Models\Ward;
use SebastianBergmann\CodeCoverage\Util\Percentage;

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

        $ward_datas = Ward::where('corporation_id', $corporation->id)
            ->where('status', 'active')
            ->get();

        $wards_per_zones = Ward::where('corporation_id', $corporation->id)
            ->where('status', 'active')
            ->select('zone', DB::raw('count(*) as total'))
            ->groupBy('zone')
            ->get();

        $misTableName = "mis_corporation_{$corporation->id}";

        $totalBuilding = 0;
        $totalSurveyedBuilding = 0;
        $totalSurveyedAssessment = 0;
        $totalShops = 0;
        $totalShopDataCount = 0;
        $totalShopDataInMis = 0;
        $totalShopDataNotInMis = 0;

        $totalMis = Schema::hasTable($misTableName)
            ? DB::table($misTableName)->count()
            : 0;

        $wards = [];
        $shopTableName = "shop_corporation_{$corporation->id}";
        $shopCount = DB::table($shopTableName)->count();
        $totalShops = $shopCount;

        foreach ($wards_per_zones as $wards_per_zone) {

            $wardlists = Ward::where('corporation_id', $corporation->id)
                ->where('zone', $wards_per_zone->zone)
                ->get();

            foreach ($wardlists as $wardlist) {

                $pointDataTableName = $this->getPointDataTable(
                    $corporation->id,
                    $wardlist->ward_no,
                    $wardlist->zone
                );

                $polygonDataTableName = $this->getPolygonDataTable(
                    $corporation->id,
                    $wardlist->ward_no,
                    $wardlist->zone
                );

                $polygonsTableName = $this->getPolygonTable(
                    $corporation->id,
                    $wardlist->ward_no,
                    $wardlist->zone
                );

                $zone = trim(strtolower($wardlist->zone));

                $shopTableName = "shop_corporation_{$corporation->id}";
                $shopDataTableName = "shopdata_{$corporation->id}_{$zone}_{$wardlist->ward_no}";

                // Polygon Count
                if ($polygonsTableName && Schema::hasTable($polygonsTableName)) {
                    $polygonCount = DB::table($polygonsTableName)->count();
                    $totalBuilding += $polygonCount;
                } else {
                    $polygonCount = 0;
                }

                // PolygonData Count (Unique GISID)
                if ($polygonDataTableName && Schema::hasTable($polygonDataTableName)) {
                    $polygonDataCount = DB::table($polygonDataTableName)
                        ->distinct('gisid')
                        ->count('gisid');

                    $totalSurveyedBuilding += $polygonDataCount;
                } else {
                    $polygonDataCount = 0;
                }

                // PointData Count
                if ($pointDataTableName && Schema::hasTable($pointDataTableName)) {
                    $pointDataCount = DB::table($pointDataTableName)->count();
                    $totalSurveyedAssessment += $pointDataCount;
                } else {
                    $pointDataCount = 0;
                }

                // Shop Count
                $shopCount = 0;
                $shopDataCount = 0;
                $shopDataInMisCount = 0;
                $shopDataNotinMisCount = 0;

                if (Schema::hasTable($shopTableName)) {


                    if (Schema::hasTable($shopDataTableName)) {
                        $shopDataCount = DB::table($shopDataTableName)->count();
                        $totalShopDataCount += $shopDataCount;

                        $shopDataInMisCount = DB::table($shopDataTableName)
                            ->whereIn('prof_tax_assessment', function ($query) use ($shopTableName) {
                                $query->select('prof_tax_assessment')
                                    ->from($shopTableName);
                            })
                            ->count();

                        $shopDataNotinMisCount = $shopDataCount - $shopDataInMisCount;

                        $totalShopDataInMis += $shopDataInMisCount;
                        $totalShopDataNotInMis += $shopDataNotinMisCount;
                    }
                }

                $wards[] = [
                    'ward_id' => $wardlist->id,
                    'ward_no' => $wardlist->ward_no,
                    'zone' => $wardlist->zone,
                    'total_buildings' => $polygonCount,
                    'surveyed_buildings' => $polygonDataCount,
                    'surveyed_assessment' => $pointDataCount,
                    'mis_count' => $totalMis,
                    'shop_count' => $shopCount,
                    'shop_data_count' => $shopDataCount,
                    'shop_data_in_mis_count' => $shopDataInMisCount,
                    'shop_data_not_in_mis_count' => $shopDataNotinMisCount,
                ];
            }
        }

        // Calculate survey percentage
        $survey_percentage = $totalBuilding > 0
            ? round(($totalSurveyedBuilding / $totalBuilding) * 100, 1)
            : 0;

        // Return view with all data
        return view('corporation.dashboard', [
            'corporation' => $corporation,
            'ward_count' => $ward_datas->count(),
            'total_building' => $totalBuilding,
            'total_surveyed_building' => $totalSurveyedBuilding,
            'total_surveyed_assessment' => $totalSurveyedAssessment,
            'total_mis' => $totalMis,
            'total_shops' => $totalShops,
            'total_shop_data_count' => $totalShopDataCount,
            'total_shop_data_in_mis' => $totalShopDataInMis,
            'total_shop_data_not_in_mis' => $totalShopDataNotInMis,
            'survey_percentage' => $survey_percentage,
            'wards' => $wards,
            'wards_per_zones' => $wards_per_zones,
        ]);
    }
    //analytics
    public function Analystics()
    {
        $user = Auth::guard('corporation')->user();

        if (!$user) {
            return redirect()->route('corporation.login');
        }

        $corporation = Corporation::find($user->corporation_id);

        if (!$corporation) {
            return back()->with('error', 'Corporation not found');
        }

        $ward_datas = Ward::where('corporation_id', $corporation->id)
            ->where('status', 'active')
            ->get();

        $wards_per_zones = Ward::where('corporation_id', $corporation->id)
            ->where('status', 'active')
            ->select('zone', DB::raw('count(*) as total'))
            ->groupBy('zone')
            ->get();

        $misTableName = "mis_corporation_{$corporation->id}";

        $totalBuilding = 0;
        $totalSurveyedBuilding = 0;
        $totalSurveyedAssessment = 0;
        $totalShops = 0;
        $totalShopDataCount = 0;
        $totalShopDataInMis = 0;
        $totalShopDataNotInMis = 0;

        $totalMis = Schema::hasTable($misTableName)
            ? DB::table($misTableName)->count()
            : 0;

        // Get paginated wards
        $paginatedWards = Ward::where('corporation_id', $corporation->id)
            ->where('status', 'active')
            ->orderBy('zone')
            ->orderBy('ward_no')
            ->paginate(15); // 15 wards per page, you can adjust this number

        $wards = [];
        $shopTableName = "shop_corporation_{$corporation->id}";
        $totalShops = DB::table($shopTableName)->count();

        foreach ($paginatedWards as $wardlist) {
            $pointDataTableName = $this->getPointDataTable(
                $corporation->id,
                $wardlist->ward_no,
                $wardlist->zone
            );

            $polygonDataTableName = $this->getPolygonDataTable(
                $corporation->id,
                $wardlist->ward_no,
                $wardlist->zone
            );

            $polygonsTableName = $this->getPolygonTable(
                $corporation->id,
                $wardlist->ward_no,
                $wardlist->zone
            );

            $zone = trim(strtolower($wardlist->zone));

            $shopTableName = "shop_corporation_{$corporation->id}";
            $shopDataTableName = "shopdata_{$corporation->id}_{$zone}_{$wardlist->ward_no}";

            // Polygon Count
            if ($polygonsTableName && Schema::hasTable($polygonsTableName)) {
                $polygonCount = DB::table($polygonsTableName)->count();
                $totalBuilding += $polygonCount;
            } else {
                $polygonCount = 0;
            }

            // PolygonData Count (Unique GISID)
            if ($polygonDataTableName && Schema::hasTable($polygonDataTableName)) {
                $polygonDataCount = DB::table($polygonDataTableName)
                    ->distinct('gisid')
                    ->count('gisid');

                $totalSurveyedBuilding += $polygonDataCount;
            } else {
                $polygonDataCount = 0;
            }

            // PointData Count
            if ($pointDataTableName && Schema::hasTable($pointDataTableName)) {
                $pointDataCount = DB::table($pointDataTableName)->count();
                $totalSurveyedAssessment += $pointDataCount;
            } else {
                $pointDataCount = 0;
            }

            // Shop Count
            $shopCount = 0;
            $shopDataCount = 0;
            $shopDataInMisCount = 0;
            $shopDataNotinMisCount = 0;

            if (Schema::hasTable($shopTableName)) {
                if (Schema::hasTable($shopDataTableName)) {
                    $shopDataCount = DB::table($shopDataTableName)->count();
                    $totalShopDataCount += $shopDataCount;

                    $shopDataInMisCount = DB::table($shopDataTableName)
                        ->whereIn('prof_tax_assessment', function ($query) use ($shopTableName) {
                            $query->select('prof_tax_assessment')
                                ->from($shopTableName);
                        })
                        ->count();

                    $shopDataNotinMisCount = $shopDataCount - $shopDataInMisCount;

                    $totalShopDataInMis += $shopDataInMisCount;
                    $totalShopDataNotInMis += $shopDataNotinMisCount;
                }
            }

            $wards[] = [
                'ward_id' => $wardlist->id,
                'ward_no' => $wardlist->ward_no,
                'zone' => $wardlist->zone,
                'drone_image' => $wardlist->drone_image,
                'total_buildings' => $polygonCount,
                'surveyed_buildings' => $polygonDataCount,
                'surveyed_assessment' => $pointDataCount,
                'mis_count' => $totalMis,
                'shop_count' => $shopCount,
                'shop_data_count' => $shopDataCount,
                'shop_data_in_mis_count' => $shopDataInMisCount,
                'shop_data_not_in_mis_count' => $shopDataNotinMisCount,
            ];
        }

        // Calculate survey percentage
        $survey_percentage = $totalBuilding > 0
            ? round(($totalSurveyedBuilding / $totalBuilding) * 100, 1)
            : 0;

        // Return view with all data
        return view('corporation.analystics', [
            'corporation' => $corporation,
            'ward_count' => $ward_datas->count(),
            'total_building' => $totalBuilding,
            'total_surveyed_building' => $totalSurveyedBuilding,
            'total_surveyed_assessment' => $totalSurveyedAssessment,
            'total_mis' => $totalMis,
            'total_shops' => $totalShops,
            'total_shop_data_count' => $totalShopDataCount,
            'total_shop_data_in_mis' => $totalShopDataInMis,
            'total_shop_data_not_in_mis' => $totalShopDataNotInMis,
            'survey_percentage' => $survey_percentage,
            'wards' => $wards,
            'wards_pagination' => $paginatedWards, // Add pagination object
            'wards_per_zones' => $wards_per_zones,
        ]);
    }

    private function calculateWardVariations($corporationId, $zone, $wardNo)
    {
        $zone = strtolower(trim($zone));
        $wardNo = (int)$wardNo;

        $polygonsTableName = "polygon_{$corporationId}_{$zone}_{$wardNo}";
        $polygonDataTableName = "polygondata_{$corporationId}_{$zone}_{$wardNo}";
        $pointDataTableName = "pointdata_{$corporationId}_{$zone}_{$wardNo}";
        $misTableName = "mis_corporation_{$corporationId}";

        if (!Schema::hasTable($polygonsTableName) || !Schema::hasTable($pointDataTableName)) {
            return [
                'area_variation_count' => 0,
                'usage_variation_count' => 0,
                'area_variation_percentage' => 0,
                'usage_variation_percentage' => 0
            ];
        }

        $polygons = DB::table($polygonsTableName)->get();
        $polygonDatas = Schema::hasTable($polygonDataTableName) ?
            DB::table($polygonDataTableName)->get()->keyBy('gisid') : collect();

        $pointDataQuery = DB::table($pointDataTableName . ' as pd')
            ->leftJoin($misTableName . ' as mis', 'pd.assessment', '=', 'mis.assessment')
            ->select(
                'pd.point_gisid',
                'pd.assessment',
                'pd.qcsqfeet',
                'pd.bill_usage',
                'mis.plot_area'
            );

        $pointDatas = $pointDataQuery->get();

        $pointDataByGisid = [];
        foreach ($pointDatas as $pointData) {
            $gisid = $pointData->point_gisid;
            if (!isset($pointDataByGisid[$gisid])) {
                $pointDataByGisid[$gisid] = [];
            }
            $pointDataByGisid[$gisid][] = $pointData;
        }

        $areaVariationCount = 0;
        $usageVariationCount = 0;
        $validBuildingsCount = 0;

        foreach ($polygons as $polygon) {
            $gisid = $polygon->gisid;
            $polygonSqfeet = floatval($polygon->sqfeet ?? 0);

            $polyData = $polygonDatas->get($gisid);

            if ($polyData) {
                $numberFloor = floatval($polyData->number_floor ?? 0);
                $basement = floatval($polyData->basement ?? 0);
                $buildingArea = ($numberFloor + $basement) * $polygonSqfeet;
                $buildingUsage = $polyData->building_usage ?? null;
            } else {
                $buildingArea = $polygonSqfeet;
                $buildingUsage = null;
            }

            $assessmentArea = 0;
            $hasUsageMismatch = false;

            if (isset($pointDataByGisid[$gisid])) {
                foreach ($pointDataByGisid[$gisid] as $pointData) {
                    $pointArea = 0;
                    if (isset($pointData->qcsqfeet) && $pointData->qcsqfeet > 0) {
                        $pointArea = floatval($pointData->qcsqfeet);
                    } elseif (isset($pointData->plot_area) && $pointData->plot_area > 0) {
                        $pointArea = floatval($pointData->plot_area);
                    }
                    $assessmentArea += $pointArea;

                    $pointUsage = $pointData->bill_usage ?? null;
                    if ($buildingUsage && $pointUsage && strtoupper(trim($buildingUsage)) != strtoupper(trim($pointUsage))) {
                        $hasUsageMismatch = true;
                    }
                }
            }

            if ($buildingArea > 0 && $assessmentArea > 0) {
                $validBuildingsCount++;

                $areaDiff = abs($buildingArea - $assessmentArea);
                if ($areaDiff > 1) {
                    $areaVariationCount++;
                }

                if ($hasUsageMismatch) {
                    $usageVariationCount++;
                }
            }
        }

        return [
            'area_variation_count' => $areaVariationCount,
            'usage_variation_count' => $usageVariationCount,
            'area_variation_percentage' => $validBuildingsCount > 0 ? round(($areaVariationCount / $validBuildingsCount) * 100, 1) : 0,
            'usage_variation_percentage' => $validBuildingsCount > 0 ? round(($usageVariationCount / $validBuildingsCount) * 100, 1) : 0,
        ];
    }
    public function viewVariations($ward_no)
    {
        $user = Auth::user();

        $warddetail = Ward::where('corporation_id', $user->corporation_id)
            ->where('ward_no', $ward_no)
            ->first();

        if (!$warddetail) {
            return back()->with('error', 'Ward not found');
        }

        $zone = strtolower(trim($warddetail->zone));
        $wardNo = (int) $warddetail->ward_no;
        $corp = (int) $warddetail->corporation_id;

        $misTableName = "mis_corporation_{$corp}";

        // Dynamic table names
        $polygonsTableName = "polygon_{$corp}_{$zone}_{$wardNo}";
        $pointsTableName   = "point_{$corp}_{$zone}_{$wardNo}";

        $pointDataTable    = $this->getPointDataTable($corp, $wardNo, $zone);
        $polygonDataTable  = $this->getPolygonDataTable($corp, $wardNo, $zone);

        $shopTableName     = "shopdata_{$corp}_{$zone}_{$wardNo}";

        // Get all polygons
        $polygons = DB::table($polygonsTableName)->get();

        $results = [];

        foreach ($polygons as $polygon) {

            // Building data
            $buildingData = DB::table($polygonDataTable)
                ->where('gisid', $polygon->gisid)
                ->first();

            // Point data + MIS
            $pointDatas = DB::table($pointDataTable . ' as pd')
                ->leftJoin($misTableName . ' as mis', 'pd.assessment', '=', 'mis.assessment')
                ->where('pd.point_gisid', $polygon->gisid)
                ->select(
                    'pd.*',
                    'mis.owner_name as mis_owner_name',
                    'mis.plot_area as mis_plot_area',
                    'mis.half_year_tax as mis_half_year_tax',
                    'mis.usage as mis_usage'
                )
                ->get();

            /**
             * IMPORTANT
             * Point data must exist
             */
            if ($pointDatas->isEmpty()) {
                continue;
            }

            // Point count
            $misTotalArea = $pointDatas->sum('mis_plot_area');



            $numberFloor = (float) ($buildingData->number_floor ?? 0);
            $basement    = (float) ($buildingData->basement ?? 0);
            $percentage  = (float) ($buildingData->percentage ?? 0);

            $polygonSqft = (float) ($polygon->sqfeet ?? 0);

            // Drone calculated area
            $droneArea = ($numberFloor + $basement + ($percentage / 100)) * $polygonSqft;



            // Difference
            $areaDifference = $droneArea - $misTotalArea;

            /**
             * AREA VARIATION STATUS
             */
            if ($areaDifference > 100) {
                $areaVariation = 'EXCESS';
            } elseif ($areaDifference < -100) {
                $areaVariation = 'SHORT';
            } else {
                $areaVariation = 'MATCHED';
            }

            /**
             * USAGE VARIATION
             */

            $usageVariation = false;
            $usageMismatches = [];

            foreach ($pointDatas as $pd) {

                $surveyUsage = strtolower(trim($pd->bill_usage ?? ''));
                $misUsage    = strtolower(trim($pd->mis_usage ?? ''));

                if ($surveyUsage != $misUsage) {

                    $usageVariation = true;

                    $usageMismatches[] = [
                        'assessment'   => $pd->assessment,
                        'survey_usage' => $pd->bill_usage,
                        'mis_usage'    => $pd->mis_usage,
                    ];
                }
            }

            // Final result
            $results[] = [
                'gisid'              => $polygon->gisid,
                'sqfeet'             => $polygonSqft,

                'building_name'      => $buildingData->building_name ?? '',
                'road_name'          => $buildingData->road_name ?? '',
                'building_usage'     => $buildingData->building_usage ?? '',

                'number_floor'       => $numberFloor,
                'basement'           => $basement,
                'percentage'         => $percentage,

                'surveyed_points'    => 1,
                'assessment_count'   => $pointDatas->count(),


                // Area variation
                'drone_area'         => round($droneArea, 2),
                'mis_total_area'     => round($misTotalArea, 2),
                'area_difference'    => round($areaDifference, 2),
                'area_variation'     => $areaVariation,

                // Usage variation
                'usage_variation'    => $usageVariation,
                'usage_mismatches'   => $usageMismatches,

                // Raw data
                'assessments'        => $pointDatas,

                'building_data'      => $buildingData,
            ];
        }

        return view('corporation.variations', compact(
            'results',
            'warddetail',
            'ward_no'
        ));
    }
    /**
     * Export ward data to Excel with building variations
     */
    public function mapDownloadExcel($ward_no)
    {
        $user = Auth::guard('corporation')->user();

        if (!$user) {
            return redirect()->route('corporation.login');
        }

        $warddetail = Ward::where('corporation_id', $user->corporation_id)
            ->where('ward_no', $ward_no)
            ->first();

        if (!$warddetail) {
            return back()->with('error', 'Ward not found');
        }

        $zone = strtolower(trim($warddetail->zone));
        $wardNo = (int)$warddetail->ward_no;
        $corp = (int)$warddetail->corporation_id;

        $polygonsTableName = "polygon_{$corp}_{$zone}_{$wardNo}";
        $polygonDataTableName = "polygondata_{$corp}_{$zone}_{$wardNo}";
        $pointDataTableName = "pointdata_{$corp}_{$zone}_{$wardNo}";
        $misTableName = "mis_corporation_{$corp}";

        if (!Schema::hasTable($polygonsTableName)) {
            return back()->with('error', 'Building data not found for this ward');
        }

        $polygons = DB::table($polygonsTableName)->get();
        $polygonDatas = Schema::hasTable($polygonDataTableName)
            ? DB::table($polygonDataTableName)->get()->keyBy('gisid')
            : collect();

        $pointDatas = collect();
        $pointDataByGisid = [];

        if (Schema::hasTable($pointDataTableName)) {
            $pointDataQuery = DB::table($pointDataTableName . ' as pd')
                ->leftJoin($misTableName . ' as mis', 'pd.assessment', '=', 'mis.assessment')
                ->select(
                    'pd.point_gisid',
                    'pd.assessment',
                    'pd.qcsqfeet',
                    'pd.bill_usage',
                    'pd.qcusage',
                    'mis.plot_area',
                    'mis.owner_name',
                    'mis.road_name'
                );

            $pointDatas = $pointDataQuery->get();

            foreach ($pointDatas as $pointData) {
                $gisid = $pointData->point_gisid;
                if (!isset($pointDataByGisid[$gisid])) {
                    $pointDataByGisid[$gisid] = [];
                }
                $pointDataByGisid[$gisid][] = $pointData;
            }
        }

        $excelData = [];
        $rowNumber = 1;

        $excelData[] = [
            'S.No',
            'GIS ID',
            'Building Sq Feet',
            'Number of Floors',
            'Basement',
            'Floor Percentage',
            'Total Building Area',
            'Building Usage',
            'Total Assessment Area',
            'Area Variation',
            'Area Variation Status',
            'Number of Bills',
            'Usage Variation Status',
            'Negative Area Variation',
            'Variation Percentage',
            'Owner Name',
            'Address',
            'Road Name'
        ];

        foreach ($polygons as $polygon) {
            $gisid = $polygon->gisid;
            $polygonSqfeet = floatval($polygon->sqfeet ?? 0);

            $polyData = $polygonDatas->get($gisid);
            $numberFloor = 0;
            $basement = 0;
            $floorPercentage = 100;
            $buildingUsage = null;
            $totalBuildingArea = $polygonSqfeet;

            if ($polyData) {
                $numberFloor = floatval($polyData->number_floor ?? 0);
                $basement = floatval($polyData->basement ?? 0);
                $floorPercentage = floatval($polyData->percentage ?? 100);
                $buildingUsage = $polyData->building_usage ?? null;
                $totalBuildingArea = $polygonSqfeet * ($numberFloor + ($floorPercentage / 100) + $basement);
            }

            $assessmentArea = 0;
            $assessmentCount = 0;
            $hasUsageMismatch = false;
            $ownerName = '';
            $address = '';
            $roadName = '';

            if (isset($pointDataByGisid[$gisid])) {
                $assessmentCount = count($pointDataByGisid[$gisid]);

                foreach ($pointDataByGisid[$gisid] as $pointData) {
                    $pointArea = 0;
                    if (isset($pointData->qcsqfeet) && $pointData->qcsqfeet > 0) {
                        $pointArea = floatval($pointData->qcsqfeet);
                    } elseif (isset($pointData->plot_area) && $pointData->plot_area > 0) {
                        $pointArea = floatval($pointData->plot_area);
                    }
                    $assessmentArea += $pointArea;

                    $pointUsage = $pointData->bill_usage ?? $pointData->qcusage ?? null;
                    if ($buildingUsage && $pointUsage && strtoupper(trim($buildingUsage)) != strtoupper(trim($pointUsage))) {
                        $hasUsageMismatch = true;
                    }

                    if (empty($ownerName) && isset($pointData->owner_name)) {
                        $ownerName = $pointData->owner_name;
                        $address = $pointData->road_name ?? '';
                        $roadName = $pointData->road_name ?? '';
                    }
                }
            }

            $areaVariation = $totalBuildingArea - $assessmentArea;
            $areaVariationAbs = abs($areaVariation);
            $hasAreaVariation = $areaVariationAbs > 1;
            $isNegativeVariation = $areaVariation < 0;
            $variationPercentage = $totalBuildingArea > 0 ? round(($areaVariationAbs / $totalBuildingArea) * 100, 2) : 0;

            $areaStatus = $hasAreaVariation ? 'VARIATION' : 'MATCH';
            $usageStatus = $hasUsageMismatch ? 'VARIATION' : 'MATCH';
            $negativeStatus = $isNegativeVariation ? 'YES' : 'NO';

            $excelData[] = [
                $rowNumber++,
                $gisid,
                $polygonSqfeet,
                $numberFloor,
                $basement,
                $floorPercentage . '%',
                round($totalBuildingArea, 2),
                $buildingUsage ?? 'N/A',
                round($assessmentArea, 2),
                round($areaVariation, 2),
                $areaStatus,
                $assessmentCount,
                $usageStatus,
                $negativeStatus,
                $variationPercentage . '%',
                $ownerName,
                $address,
                $roadName
            ];
        }

        $totalBuildings = count($polygons);
        $buildingsWithAreaVariation = 0;
        $buildingsWithUsageVariation = 0;
        $buildingsWithNegativeVariation = 0;
        $totalBuildingAreaSum = 0;
        $totalAssessmentAreaSum = 0;

        foreach ($excelData as $index => $row) {
            if ($index === 0) continue;
            if ($row[10] === 'VARIATION') $buildingsWithAreaVariation++;
            if ($row[12] === 'VARIATION') $buildingsWithUsageVariation++;
            if ($row[13] === 'YES') $buildingsWithNegativeVariation++;
            $totalBuildingAreaSum += floatval($row[6]);
            $totalAssessmentAreaSum += floatval($row[8]);
        }

        return $this->generateExcel($excelData, $warddetail, [
            'totalBuildings' => $totalBuildings,
            'areaVariationCount' => $buildingsWithAreaVariation,
            'usageVariationCount' => $buildingsWithUsageVariation,
            'negativeVariationCount' => $buildingsWithNegativeVariation,
            'totalBuildingArea' => round($totalBuildingAreaSum, 2),
            'totalAssessmentArea' => round($totalAssessmentAreaSum, 2),
            'wardName' => "Ward {$warddetail->ward_no}",
            'zone' => $warddetail->zone,
            'corporationName' => $user->name ?? 'Corporation'
        ]);
    }

    private function generateExcel($data, $ward, $summary)
    {
        $filename = "ward_{$ward->ward_no}_building_variations_" . date('Ymd_His') . ".xls";

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        echo '<html>';
        echo '<head><meta charset="UTF-8">';
        echo '<title>Ward ' . $ward->ward_no . ' Building Variations Report</title>';
        echo '<style>';
        echo 'th { background-color: #4472C4; color: white; border: 1px solid #000; padding: 8px; }';
        echo 'td { border: 1px solid #ccc; padding: 6px; }';
        echo '.summary-table { margin-bottom: 20px; border-collapse: collapse; width: 100%; }';
        echo '.summary-table td { padding: 8px; }';
        echo '.header { font-size: 18px; font-weight: bold; margin-bottom: 20px; }';
        echo '.subheader { font-size: 14px; margin-bottom: 20px; color: #666; }';
        echo '.variation-match { background-color: #C6EFCE; }';
        echo '.variation-mismatch { background-color: #FFC7CE; }';
        echo '</style></head><body>';

        echo '<div class="header">';
        echo '<h2>' . htmlspecialchars($summary['corporationName']) . '</h2>';
        echo '<h3>Building Variation Report - ' . htmlspecialchars($summary['wardName']) . ' (' . htmlspecialchars($summary['zone']) . ' Zone)</h3>';
        echo '</div>';
        echo '<div class="subheader">Generated on: ' . date('d-m-Y H:i:s') . '<br></div>';

        echo '<h3>Summary Statistics</h3>';
        echo '<table class="summary-table" border="1" cellpadding="5" cellspacing="0">';
        echo '<tr style="background-color: #E6E6E6;"><td width="50%"><strong>Total Buildings:</strong></td><td>' . $summary['totalBuildings'] . '</td></tr>';
        echo '<tr><td><strong>Buildings with Area Variation:</strong></td><td>' . $summary['areaVariationCount'] . ' (' . round(($summary['areaVariationCount'] / max(1, $summary['totalBuildings'])) * 100, 2) . '%)</td></tr>';
        echo '<tr style="background-color: #E6E6E6;"><td><strong>Buildings with Usage Variation:</strong></td><td>' . $summary['usageVariationCount'] . ' (' . round(($summary['usageVariationCount'] / max(1, $summary['totalBuildings'])) * 100, 2) . '%)</td></tr>';
        echo '<tr><td><strong>Buildings with Negative Variation:</strong></td><td>' . $summary['negativeVariationCount'] . ' (' . round(($summary['negativeVariationCount'] / max(1, $summary['totalBuildings'])) * 100, 2) . '%)</td></tr>';
        echo '<tr style="background-color: #E6E6E6;"><td><strong>Total Building Area:</strong></td><td>' . number_format($summary['totalBuildingArea'], 2) . ' sq ft</td></tr>';
        echo '<tr><td><strong>Total Assessment Area:</strong></td><td>' . number_format($summary['totalAssessmentArea'], 2) . ' sq ft</td></tr>';
        echo '<tr style="background-color: #E6E6E6;"><td><strong>Total Area Variation:</strong></td><td>' . number_format($summary['totalBuildingArea'] - $summary['totalAssessmentArea'], 2) . ' sq ft</td></tr>';
        echo '</table><br><br>';

        echo '<h3>Detailed Building Data</h3>';
        echo '<table border="1" cellpadding="5" cellspacing="0">';

        echo '<tr>';
        foreach ($data[0] as $header) {
            echo '<th>' . htmlspecialchars($header) . '</th>';
        }
        echo '</tr>';

        for ($i = 1; $i < count($data); $i++) {
            $row = $data[$i];
            $rowClass = '';
            if ($row[10] === 'VARIATION' || $row[12] === 'VARIATION') {
                $rowClass = 'class="variation-mismatch"';
            } elseif ($row[10] === 'MATCH' && $row[12] === 'MATCH') {
                $rowClass = 'class="variation-match"';
            }
            echo '<tr ' . $rowClass . '>';
            foreach ($row as $value) {
                echo '<td>' . htmlspecialchars($value) . '</td>';
            }
            echo '</tr>';
        }
        echo '</table>';

        echo '<br><br>';
        echo '<table border="0" cellpadding="5">';
        echo '<tr><td style="background-color: #C6EFCE; border:1px solid #000;">&nbsp;&nbsp;&nbsp;&nbsp;</td><td><strong>Match:</strong> No variations found</td></tr>';
        echo '<tr><td style="background-color: #FFC7CE; border:1px solid #000;">&nbsp;&nbsp;&nbsp;&nbsp;</td><td><strong>Variation:</strong> Area or Usage mismatch detected</td></tr>';
        echo '</table></body></html>';
        exit;
    }

    public function mapView($ward_no)
    {
        $userId = Auth::user();
        $warddetail = Ward::where('corporation_id', $userId->corporation_id)
            ->where('ward_no', $ward_no)
            ->first();

        if (!$warddetail) {
            return back()->with('error', 'Ward not found');
        }

        $zone = strtolower(trim($warddetail->zone));
        $wardNo = (int)$warddetail->ward_no;
        $corp = (int)$warddetail->corporation_id;

        // Table names
        $polygonsTableName = "polygon_{$corp}_{$zone}_{$wardNo}";
        $pointsTableName = "point_{$corp}_{$zone}_{$wardNo}";
        $linesTableName = "line_{$corp}_{$zone}_{$wardNo}";

        $pointDataTable = $this->getPointDataTable($corp, $wardNo, $zone);
        $polygonDataTable = $this->getPolygonDataTable($corp, $wardNo, $zone);

        $shopTableName = "shopdata_{$corp}_{$zone}_{$wardNo}";

        // Point Data
        $pointDatas = DB::table($pointDataTable)
            ->select(
                'id',
                'building_data_id',
                'point_gisid',
                'assessment',
                'old_assessment',
                'owner_name',
                'present_owner_name',
                'floor',
                'bill_usage',
                'phone_number',
                'old_door_no',
                'new_door_no',
                'remarks',
                'water_tax',
                'zone',
                'qcusage',
                'qcsqfeet'
            )
            ->get();

        // Polygon Data
        $polygonDatas = DB::table($polygonDataTable)
            ->select(
                'id',
                'gisid',
                'number_bill',
                'number_floor',
                DB::raw('Percentage as floor_percentage'),
                'building_usage',
                'construction_type',
                'road_name',
                'ugd',
                'basement',
                'water_connection',
                'image',
                'building_type',
                'image2',
                'remarks'
            )
            ->get();

        // Shop Data
        $shopDatas = DB::table($shopTableName)->get();

        // Group shops by point_data_id
        $shopsGrouped = $shopDatas->groupBy('point_data_id');

        // Attach shops into pointdata
        foreach ($pointDatas as $pointdata) {

            $pointdata->shops = $shopsGrouped[$pointdata->id] ?? collect();
        }

        // Group pointdata by point_gisid
        $pointGrouped = collect($pointDatas)->groupBy('point_gisid');

        // Attach pointdata into polygondata
        foreach ($polygonDatas as $polygondata) {

            $polygondata->pointdata = $pointGrouped[$polygondata->gisid] ?? collect();

            // Optional statistics
            $polygondata->total_points = count($polygondata->pointdata);

            $polygondata->total_shops = collect($polygondata->pointdata)
                ->sum(function ($point) {
                    return count($point->shops);
                });
        }



        // Get polygons (buildings) - only needed fields
        $polygons = Schema::hasTable($polygonsTableName)
            ? DB::table($polygonsTableName)->select('gisid', 'coordinates', 'sqfeet')->get()
            : [];

        // Get lines (roads) - only needed fields
        $lines = Schema::hasTable($linesTableName)
            ? DB::table($linesTableName)->select('gisid', 'coordinates')->get()
            : [];


        $ward = $warddetail;
        $points = DB::table($pointsTableName)->get();

        return view('corporation.ward-map', compact('ward', 'polygons', 'lines', 'polygonDatas', 'points'));
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

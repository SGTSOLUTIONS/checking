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
                ->paginate(15);

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
                'wards_pagination' => $paginatedWards,
                'wards_per_zones' => $wards_per_zones,
            ]);
        }

        public function viewVariations($ward_no)
        {
            $user = Auth::guard('corporation')->user();

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
            $polygonsTableName = "polygon_{$corp}_{$zone}_{$wardNo}";
            $pointTableName = "point_{$corp}_{$zone}_{$wardNo}";
            $pointDataTable = $this->getPointDataTable($corp, $wardNo, $zone);
            $polygonDataTable = $this->getPolygonDataTable($corp, $wardNo, $zone);

            /*
            |--------------------------------------------------------------------------
            | Polygon Data with Building Usage
            |--------------------------------------------------------------------------
            */
            $polygons = DB::table($polygonsTableName . ' as p')
                ->leftJoin(
                    $polygonDataTable . ' as pgd',
                    'p.gisid',
                    '=',
                    'pgd.gisid'
                )
                ->leftJoin(
                    $pointTableName . ' as pd',
                    'p.gisid',
                    '=',
                    'pd.gisid'
                )
                ->select(
                    'p.gisid',
                    'p.sqfeet',
                    'pd.coordinates',
                    'pgd.number_floor',
                    'pgd.percentage',
                    'pgd.basement',
                    'pgd.building_usage as surveyed_usage'
                )
                ->get();

            /*
            |--------------------------------------------------------------------------
            | Point Data with Assessment and Usage
            |--------------------------------------------------------------------------
            */
            $pointDatas = DB::table($pointDataTable)
                ->select('point_gisid', 'assessment', 'bill_usage as point_usage')
                ->whereNotNull('assessment')
                ->get()
                ->groupBy('point_gisid');

            /*
            |--------------------------------------------------------------------------
            | Assessment List
            |--------------------------------------------------------------------------
            */
            $assessmentList = DB::table($pointDataTable)
                ->whereNotNull('assessment')
                ->pluck('assessment')
                ->unique()
                ->toArray();

            /*
            |--------------------------------------------------------------------------
            | MIS Data with Tax Information (half_year_tax and balance)
            |--------------------------------------------------------------------------
            */
            $misData = DB::table($misTableName)
                ->whereIn('assessment', $assessmentList)
                ->select(
                    'assessment',
                    'plot_area',
                    'half_year_tax',
                    'balance',
                    DB::raw('COALESCE(half_year_tax, 0) as half_year_tax_value'),
                    DB::raw('COALESCE(balance, 0) as balance_value')
                )
                ->get()
                ->keyBy('assessment');

            /*
            |--------------------------------------------------------------------------
            | Process All Data
            |--------------------------------------------------------------------------
            */
            $allResults = [];
            $totalSqfeet = 0;
            $totalBuildings = 0;
            $totalMisPlotArea = 0;
            $totalCalculatedArea = 0;
            $totalAreaVariation = 0;
            $areaVariationCount = 0;
            $usageVariationCount = 0;

            foreach ($polygons as $index => $polygon) {
                $points = $pointDatas[$polygon->gisid] ?? collect();
                $misArea = 0;
                $misHalfYearTax = 0;
                $misBalance = 0;
                $assessmentCount = 0;
                $misUsage = null;

                // Extract coordinates and convert from EPSG:3857 to EPSG:4326
                $lat = null;
                $lng = null;

                if ($polygon->coordinates) {
                    $coordinates = $this->convert3857ToLatLng($polygon->coordinates);
                    if ($coordinates) {
                        $lat = $coordinates['lat'];
                        $lng = $coordinates['lng'];
                    }
                }

                foreach ($points as $point) {
                    $assessmentCount++;
                    $misRecord = $misData[$point->assessment] ?? null;

                    if ($misRecord) {
                        $misArea += (float) ($misRecord->plot_area ?? 0);
                        $misHalfYearTax += (float) ($misRecord->half_year_tax_value ?? 0);
                        $misBalance += (float) ($misRecord->balance_value ?? 0);
                        $misUsage = $point->point_usage; // Get usage from point data
                    }
                }

                $sqfeet = (float) ($polygon->sqfeet ?? 0);
                $floorPercentage = (float) ($polygon->percentage ?? 100);
                $numberFloor = (int) ($polygon->number_floor ?? 1);
                $basement = (int) ($polygon->basement ?? 0);
                $surveyedUsage = trim($polygon->surveyed_usage ?? '');

                // CORRECTED FORMULA: ((numberFloor + basement + (percentage/100)) * sqfeet)
                $calculatedArea = (($numberFloor + $basement + ($floorPercentage / 100)) * $sqfeet);

                $areaVariation = $calculatedArea - $misArea;
                $variationPercentage = 0;

                if ($misArea > 0) {
                    $variationPercentage = ($areaVariation / $misArea) * 100;
                }

                // Check Usage Variation
                $usageHasVariation = false;
                $usageVariationStatus = 'MATCH';
                $surveyedUsageDisplay = $surveyedUsage ?: 'N/A';
                $misUsageDisplay = $misUsage ?: 'N/A';

                if (!empty($surveyedUsage) && !empty($misUsage)) {
                    // Compare usage (case-insensitive)
                    if (strtolower(trim($surveyedUsage)) !== strtolower(trim($misUsage))) {
                        $usageHasVariation = true;
                        $usageVariationStatus = 'VARIATION';
                        $usageVariationCount++;
                    }
                } elseif (!empty($surveyedUsage) && empty($misUsage)) {
                    $usageHasVariation = true;
                    $usageVariationStatus = 'MISSING IN MIS';
                    $usageVariationCount++;
                } elseif (empty($surveyedUsage) && !empty($misUsage)) {
                    $usageHasVariation = true;
                    $usageVariationStatus = 'MISSING IN SURVEY';
                    $usageVariationCount++;
                }

                // Check Area Variation
                $areaHasVariation = abs($variationPercentage) > 0.01; // More than 0.01% difference
                if ($areaHasVariation) {
                    $areaVariationCount++;
                }

                // Add to totals (only if there's at least one assessment)
                if ($assessmentCount > 0) {
                    $totalBuildings++;
                    $totalSqfeet += $sqfeet;
                    $totalMisPlotArea += $misArea;
                    $totalCalculatedArea += $calculatedArea;
                    $totalAreaVariation += $areaVariation;
                }

                $allResults[] = [
                    'index' => $index + 1,
                    'gisid' => $polygon->gisid,
                    'sqfeet' => round($sqfeet, 2),
                    'number_floor' => $numberFloor,
                    'percentage' => $floorPercentage,
                    'basement' => $basement,
                    'surveyed_usage' => $surveyedUsageDisplay,
                    'mis_usage' => $misUsageDisplay,
                    'usage_variation' => $usageVariationStatus,
                    'mis_plot_area' => round($misArea, 2),
                    'calculated_area' => round($calculatedArea, 2),
                    'area_variation' => round($areaVariation, 2),
                    'variation_percentage' => round($variationPercentage, 2),
                    'area_variation_status' => $areaHasVariation ? 'VARIATION' : 'MATCH',
                    'half_year_tax' => round($misHalfYearTax, 2),
                    'tax_balance' => round($misBalance, 2),
                    'assessment_count' => $assessmentCount,
                    'lat' => $lat,
                    'lng' => $lng,
                    'coordinates' => $polygon->coordinates,
                ];
            }

            $avgVariationPercentage = $totalMisPlotArea > 0 ? ($totalAreaVariation / $totalMisPlotArea) * 100 : 0;

            // Summary statistics
            $summary = [
                'totalBuildings' => $totalBuildings,
                'totalSqfeet' => round($totalSqfeet, 2),
                'totalMisPlotArea' => round($totalMisPlotArea, 2),
                'totalCalculatedArea' => round($totalCalculatedArea, 2),
                'totalAreaVariation' => round($totalAreaVariation, 2),
                'avgVariationPercentage' => round($avgVariationPercentage, 2),
                'areaVariationCount' => $areaVariationCount,
                'usageVariationCount' => $usageVariationCount,
                'areaVariationPercentage' => $totalBuildings > 0 ? round(($areaVariationCount / $totalBuildings) * 100, 2) : 0,
                'usageVariationPercentage' => $totalBuildings > 0 ? round(($usageVariationCount / $totalBuildings) * 100, 2) : 0,
            ];

            // Return ALL data as JSON for client-side filtering
            return view('corporation.variations', [
                'allDataJson' => json_encode($allResults),
                'warddetail' => $warddetail,
                'summary' => $summary,
                'totalSqfeet' => round($totalSqfeet, 2),
                'totalBuildings' => $totalBuildings,  // ✅ This is correct
                'totalMisPlotArea' => round($totalMisPlotArea, 2),
                'totalCalculatedArea' => round($totalCalculatedArea, 2),
                'totalAreaVariation' => round($totalAreaVariation, 2),
                'avgVariationPercentage' => round($avgVariationPercentage, 2),
            ]);
        }

        /**
         * Convert EPSG:3857 (Web Mercator) to EPSG:4326 (Latitude/Longitude)
         *
         * @param string $coordString Format: "[x,y]" where x and y are Web Mercator meters
         * @return array|null ['lat' => latitude, 'lng' => longitude] or null if conversion fails
         */
        private function convert3857ToLatLng($coordString)
        {
            try {
                // Parse the coordinates string
                $cleaned = trim($coordString, '[]');
                $parts = explode(',', $cleaned);

                if (count($parts) < 2) {
                    return null;
                }

                $x = floatval(trim($parts[0])); // Easting / X in Web Mercator
                $y = floatval(trim($parts[1])); // Northing / Y in Web Mercator

                if (empty($x) || empty($y)) {
                    return null;
                }

                // EPSG:3857 to EPSG:4326 conversion formulas
                // Longitude = x / (R * pi/180) where R = 6378137
                $lng = $x / 6378137.0 * 180 / M_PI;

                // Latitude = (pi/2 - 2 * atan(exp(-y / R))) * 180/pi
                $lat = (M_PI_2 - 2 * atan(exp(-$y / 6378137.0))) * 180 / M_PI;

                return [
                    'lat' => round($lat, 8),
                    'lng' => round($lng, 8)
                ];
            } catch (Exception $e) {
                \Log::error('3857 conversion error: ' . $e->getMessage());
                return null;
            }
        }

        public function mapView($ward_no)
        {
            $userId = Auth::guard('corporation')->user();
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

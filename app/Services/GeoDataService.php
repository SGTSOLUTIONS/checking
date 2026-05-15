<?php

namespace App\Services;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class GeoDataService
{
    /**
     * ✅ Create Line Tables
     */
    public function createLineTables($corporationId, $zone, $wardNumber)
    {
        $lineTable = $this->generateLineTableName($corporationId, $zone, $wardNumber);
        try {
            // ✅ Create Line Table
            if (!Schema::hasTable($lineTable)) {
                Schema::create($lineTable, function (Blueprint $table) {
                    $table->id();
                    $table->string('gisid')->unique();
                    $table->string('type'); // LineString, MultiLineString
                    $table->string('road_name')->nullable();
                    $table->json('coordinates')->nullable();
                    $table->timestamps();
                    $table->softDeletes();
                });
                Log::info("✅ Line table created: {$lineTable}");
            }

            return $lineTable;
        } catch (\Exception $e) {
            Log::error("❌ Failed to create line tables: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * ✅ Create Polygon & Point Tables
     */
    public function createWardTables($corporationId, $zone, $wardNumber)
    {
        $polygonTable = $this->generatePolygonTableName($corporationId, $zone, $wardNumber);
        $pointTable = $this->generatePointTableName($corporationId, $zone, $wardNumber);
        $pointDataTable = $this->generatePointDataTableName($corporationId, $zone, $wardNumber);
        $polygonDataTable = $this->generatePolygonDataTableName($corporationId, $zone, $wardNumber);
        $shopDataTable = $this->generateShopDataTableName($corporationId, $zone, $wardNumber);

        try {
            // ✅ Create Polygon Table
            if (!Schema::hasTable($polygonTable)) {
                Schema::create($polygonTable, function (Blueprint $table) {
                    $table->id();
                    $table->string('gisid')->unique();
                    $table->string('type')->default('Polygon');
                    $table->json('coordinates')->nullable();
                    $table->string('sqfeet')->nullable();
                    $table->timestamps();
                    $table->softDeletes();
                });
                Log::info("✅ Polygon table created: {$polygonTable}");
            }

            // ✅ Create Point Table
            if (!Schema::hasTable($pointTable)) {
                Schema::create($pointTable, function (Blueprint $table) {
                    $table->id();
                    $table->string('gisid')->unique();
                    $table->string('type')->default('Point');
                    $table->json('coordinates')->nullable();
                    $table->timestamps();
                    $table->softDeletes();
                });
                Log::info("✅ Point table created: {$pointTable}");
            }

            // ✅ Create Polygon Data Table FIRST
            if (!Schema::hasTable($polygonDataTable)) {
                Schema::create($polygonDataTable, function (Blueprint $table) {
                    $table->id();
                    $table->string('gisid')->nullable();
                    $table->string('number_bill')->nullable();
                    $table->string('number_shop')->nullable();
                    $table->string('number_floor')->nullable();
                    $table->string('liftroom')->nullable();
                    $table->string('headroom')->nullable();
                    $table->string('overhead_tank')->nullable();
                    $table->string('percentage')->nullable();
                    $table->string('building_name')->nullable();
                    $table->string('building_usage')->nullable();
                    $table->string('construction_type')->nullable();
                    $table->string('road_name')->nullable();
                    $table->string('ugd')->nullable();
                    $table->string('rainwater_harvesting')->nullable();
                    $table->string('parking')->nullable();
                    $table->string('ramp')->nullable();
                    $table->string('hoarding')->nullable();
                    $table->string('cctv')->nullable();
                    $table->string('cell_tower')->nullable();
                    $table->string('solar_panel')->nullable();
                    $table->string('basement')->nullable();
                    $table->string('water_connection')->nullable();
                    $table->string('phone')->nullable();
                    $table->string('building_type')->nullable();
                    $table->string('image')->nullable();
                    $table->string('image2')->nullable();
                    $table->string('zone')->nullable();
                    $table->string('worker_name')->nullable();
                    $table->string('remarks')->nullable();
                    $table->string('corporationremarks')->nullable();
                    $table->timestamps();
                    $table->softDeletes();
                });
                Log::info("✅ Polygon data table created: {$polygonDataTable}");
            }

            // ✅ Create Point Data Table SECOND
            if (!Schema::hasTable($pointDataTable)) {
                Schema::create($pointDataTable, function (Blueprint $table) use ($polygonDataTable) {
                    $table->id();
                    $table->unsignedBigInteger('building_data_id')->nullable();
                    $table->foreign('building_data_id')
                        ->references('id')
                        ->on($polygonDataTable)
                        ->onUpdate('cascade')
                        ->onDelete('cascade');
                    $table->string('assessment_type')->nullable();
                    $table->string('point_gisid')->nullable();
                    $table->string('worker_name')->nullable();
                    $table->string('assessment')->nullable();
                    $table->string('old_assessment')->nullable();
                    $table->string('owner_name')->nullable();
                    $table->string('present_owner_name')->nullable();
                    $table->string('number_persons')->nullable();
                    $table->string('eb')->nullable();
                    $table->string('floor')->nullable();
                    $table->string('bill_usage')->nullable();
                    $table->string('aadhar_no')->nullable();
                    $table->string('ration_no')->nullable();
                    $table->string('phone_number')->nullable();
                    $table->string('old_door_no')->nullable();
                    $table->string('new_door_no')->nullable();
                    $table->string('remarks')->nullable();
                    $table->string('plot_area')->nullable();
                    $table->string('water_tax')->nullable();
                    $table->string('halfyeartax')->nullable();
                    $table->string('balance')->nullable();
                    $table->string('no_of_persons')->nullable();
                    $table->string('qcsqfeet')->nullable();
                    $table->string('qcusage')->nullable();
                    $table->string('qc_name')->nullable();
                    $table->string('qc_remarks')->nullable();
                    $table->string('zone')->nullable();
                    $table->timestamps();
                    $table->softDeletes();
                });

                Log::info("✅ Point data table created: {$pointDataTable}");
            }

            if (!Schema::hasTable($shopDataTable)) {
                Schema::create($shopDataTable, function (Blueprint $table) use ($pointDataTable) {
                    $table->id();
                    $table->unsignedBigInteger('point_data_id')->nullable();
                    $table->foreign('point_data_id')
                        ->references('id')
                        ->on($pointDataTable)
                        ->onUpdate('cascade')
                        ->onDelete('cascade');
                    $table->string('shop_floor')->nullable();
                    $table->string('shop_name')->nullable();
                    $table->string('shop_owner_name')->nullable();
                    $table->string('shop_category')->nullable();
                    $table->string('shop_mobile', 10)->nullable();
                    $table->string('license')->nullable();
                    $table->integer('number_of_employee')->nullable();
                    $table->string('type')->default('Point');
                    $table->json('coordinates')->nullable();
                    $table->timestamps();
                    $table->softDeletes();
                });

                Log::info("✅ Shop table created: {$shopDataTable}");
            }

            return [
                'polygon_table' => $polygonTable,
                'point_table' => $pointTable,
                'polygon_data_table' => $polygonDataTable,
                'point_data_table' => $pointDataTable,
                'shop_data_table' => $shopDataTable
            ];
        } catch (\Exception $e) {
            Log::error("❌ Failed to create ward tables: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Calculate polygon area in SQUARE FEET for EPSG:3857 (Web Mercator)
     *
     * @param array $coordinates - Polygon coordinates in EPSG:3857 projection
     * @return float - Area in square feet
     */
 private function calculatePolygonAreaInSquareFeet($coordinates)
{
    try {

        if (!is_array($coordinates) || empty($coordinates)) {
            Log::warning("Invalid coordinates array");
            return 0;
        }

        /**
         * Handle both:
         * Raw Polygon:
         * [
         *   [[x,y],[x,y]]
         * ]
         *
         * Flattened:
         * [
         *   [x,y],[x,y]
         * ]
         */

        if (
            isset($coordinates[0][0]) &&
            is_numeric($coordinates[0][0])
        ) {
            // Already flattened
            $ring = $coordinates;
        } else {
            // GeoJSON polygon structure
            $ring = $coordinates[0] ?? null;
        }

        if (!$ring || !is_array($ring) || count($ring) < 3) {
            Log::warning("Invalid polygon ring - need at least 3 points");
            return 0;
        }

        $areaInSqMeters = $this->calculate3857AreaInMeters($ring);

        if ($areaInSqMeters > 0 && $areaInSqMeters < 1000000) {

            $areaInSqFeet = $areaInSqMeters * 10.7639;

            $result = round($areaInSqFeet, 0);

            Log::info("Calculated area: {$result} sq ft");

            return $result;
        }

        $samplePoint = $ring[0];

        if (
            isset($samplePoint[0], $samplePoint[1]) &&
            abs($samplePoint[0]) <= 180 &&
            abs($samplePoint[1]) <= 90
        ) {

            $areaInSqMeters = $this->calculateSphericalAreaInMeters($ring);

            $areaInSqFeet = $areaInSqMeters * 10.7639;

            $result = round($areaInSqFeet, 0);

            Log::info("Calculated spherical area: {$result} sq ft");

            return $result;
        }

        Log::warning("Area calculation returned unreasonable value");

        return 0;

    } catch (\Exception $e) {

        Log::error("Area calculation failed: " . $e->getMessage());

        return 0;
    }
}
    /**
     * Calculate area for EPSG:3857 (Web Mercator) coordinates
     * Web Mercator has significant distortion, so we need to correct for latitude
     *
     * @param array $ring - Polygon ring in EPSG:3857 coordinates (meters)
     * @return float - Area in square meters (corrected)
     */
    private function calculate3857AreaInMeters($ring)
    {
        $count = count($ring);

        // First, calculate the raw planar area (this will be distorted)
        $rawArea = 0;
        for ($i = 0; $i < $count; $i++) {
            $p1 = $ring[$i];
            $p2 = $ring[($i + 1) % $count];
            $rawArea += ($p1[0] * $p2[1]) - ($p2[0] * $p1[1]);
        }
        $rawArea = abs($rawArea) / 2;

        // Calculate the centroid latitude to get the scale factor
        $centerY = 0;
        foreach ($ring as $point) {
            $centerY += $point[1];
        }
        $centerY = $centerY / $count;

        // Convert Web Mercator Y coordinate to latitude in radians
        // Formula: lat = atan(sinh(y / R)) where R = 6378137
        $R = 6378137; // Earth radius in meters
        $latitudeRad = atan(sinh($centerY / $R));

        // The scale factor for area in Web Mercator is 1 / cos(latitude)^2
        // Because distortion is proportional to sec(latitude)^2
        $scaleFactor = 1 / (pow(cos($latitudeRad), 2));

        // Apply correction
        $correctedArea = $rawArea / $scaleFactor;

        // Ensure we return a positive number
        return abs($correctedArea);
    }

    /**
     * Calculate area using spherical formula (for WGS84 degrees)
     * Returns area in square meters
     */
    private function calculateSphericalAreaInMeters($ring)
    {
        $earthRadius = 6378137; // Earth radius in meters
        $area = 0;
        $count = count($ring);

        for ($i = 0; $i < $count; $i++) {
            $p1 = $ring[$i];
            $p2 = $ring[($i + 1) % $count];

            $lon1 = deg2rad($p1[0]);
            $lat1 = deg2rad($p1[1]);
            $lon2 = deg2rad($p2[0]);
            $lat2 = deg2rad($p2[1]);

            $area += ($lon2 - $lon1) * (2 + sin($lat1) + sin($lat2));
        }

        return abs($area * $earthRadius * $earthRadius / 2);
    }

    /**
     * ✅ Store polygons & points from GeoJSON
     */
    public function storePolygonData($polygonTable, $pointTable, $geoJsonContent, $mode = 'create')
    {
        set_time_limit(600);

        try {
            $geoData = json_decode($geoJsonContent, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid GeoJSON format');
            }

            if (!isset($geoData['features'])) {
                throw new \Exception('GeoJSON missing "features" key');
            }

            $processedGisIds = [];
            $featuresToProcess = [];
            $duplicateGisIds = [];
            $skippedFeatures = 0;

            Log::info("📊 Starting Polygon GeoJSON processing. Total features: " . count($geoData['features']));

            foreach ($geoData['features'] as $index => $feature) {
                if ($index % 100 === 0) {
                    Log::info("🔍 Processing polygon feature {$index} of " . count($geoData['features']));
                }

                $geometryType = $feature['geometry']['type'] ?? null;
                $coords = $feature['geometry']['coordinates'] ?? null;

                $gisid = $feature['properties']['GIS_ID'] ??
                    $feature['properties']['gisid'] ??
                    $feature['properties']['GisId'] ??
                    uniqid('GIS_');

                if (in_array($gisid, $processedGisIds)) {
                    $duplicateGisIds[] = $gisid;
                    $skippedFeatures++;
                    continue;
                }

                if ($mode === 'update') {
                    $existingRecord = DB::table($polygonTable)->where('gisid', $gisid)->first();
                    $action = $existingRecord ? 'update' : 'create';
                } else {
                    $existingRecord = DB::table($polygonTable)->where('gisid', $gisid)->first();
                    if ($existingRecord) {
                        $duplicateGisIds[] = $gisid;
                        $skippedFeatures++;
                        continue;
                    }
                    $action = 'create';
                }

                $processedGisIds[] = $gisid;
                $featuresToProcess[] = [
                    'index' => $index,
                    'gisid' => $gisid,
                    'geometry_type' => $geometryType,
                    'coordinates' => $coords,
                    'action' => $action
                ];
            }

            if (count($featuresToProcess) === 0) {
                return [
                    'success' => false,
                    'message' => 'All polygon features were skipped due to duplicate GIS_IDs.',
                    'total_features' => count($geoData['features']),
                    'skipped_features' => $skippedFeatures,
                    'duplicate_gisids' => array_unique($duplicateGisIds)
                ];
            }

            $successfulFeatures = 0;
            $failedFeatures = 0;

            DB::beginTransaction();

            try {
                foreach ($featuresToProcess as $featureIndex => $feature) {
                    try {
                        $gisid = $feature['gisid'];
                        $geometryType = $feature['geometry_type'];
                        $coords = $feature['coordinates'];
                        $action = $feature['action'];

                        if (!$coords) {
                            $failedFeatures++;
                            continue;
                        }

                        // Calculate area in square feet
                        $sqfeet = $this->calculatePolygonAreaInSquareFeet($coords);

                        $flattened = $this->flattenCoordinates($geometryType, $coords);

                        if (empty($flattened)) {
                            $failedFeatures++;
                            continue;
                        }

                        $polygonData = [
                            'type' => 'Polygon',
                            'coordinates' => json_encode($flattened, JSON_UNESCAPED_UNICODE),
                            'sqfeet' => (string)$sqfeet,
                            'updated_at' => now()
                        ];

                        if ($action === 'create') {
                            $polygonData['created_at'] = now();
                            DB::table($polygonTable)->insert(array_merge(['gisid' => $gisid], $polygonData));
                        } else {
                            DB::table($polygonTable)
                                ->where('gisid', $gisid)
                                ->update($polygonData);
                        }

                        $midpoint = $this->calculateMidpoint($flattened);

                        if ($midpoint) {
                            $pointData = [
                                'type' => 'Point',
                                'coordinates' => json_encode($midpoint),
                                'updated_at' => now()
                            ];

                            if ($action === 'create') {
                                $pointData['created_at'] = now();
                                DB::table($pointTable)->insert(array_merge(['gisid' => $gisid], $pointData));
                            } else {
                                DB::table($pointTable)
                                    ->where('gisid', $gisid)
                                    ->update($pointData);
                            }
                        }

                        $successfulFeatures++;

                        if ($featureIndex % 50 === 0) {
                            Log::info("📝 Processed polygon feature {$featureIndex} of " . count($featuresToProcess));
                        }
                    } catch (\Exception $e) {
                        $failedFeatures++;
                        Log::error("❌ Failed to process polygon feature: " . $e->getMessage());
                    }
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

            $result = [
                'success' => true,
                'message' => 'Polygon GeoJSON data processed successfully',
                'total_features' => count($geoData['features']),
                'processed_features' => count($featuresToProcess),
                'successful_features' => $successfulFeatures,
                'skipped_features' => $skippedFeatures,
                'failed_features' => $failedFeatures,
                'duplicate_gisids' => array_unique($duplicateGisIds),
                'mode' => $mode
            ];

            Log::info("✅ Polygon processing completed: " . json_encode($result));
            return $result;
        } catch (\Exception $e) {
            Log::error("❌ Failed to process Polygon GeoJSON: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to process Polygon GeoJSON: ' . $e->getMessage()
            ];
        }
    }

    /**
     * ✅ Store line data from GeoJSON
     */
    public function storeLineData($lineTable, $geoJsonContent, $mode = 'create')
    {
        set_time_limit(600);

        try {
            $geoData = json_decode($geoJsonContent, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid GeoJSON format');
            }

            if (!isset($geoData['features'])) {
                throw new \Exception('GeoJSON missing "features" key');
            }

            $processedGisIds = [];
            $featuresToProcess = [];
            $duplicateGisIds = [];
            $skippedFeatures = 0;

            Log::info("📊 Starting Line GeoJSON processing. Total features: " . count($geoData['features']));

            foreach ($geoData['features'] as $index => $feature) {
                if ($index % 100 === 0) {
                    Log::info("🔍 Processing line feature {$index} of " . count($geoData['features']));
                }

                $geometryType = $feature['geometry']['type'] ?? null;
                $coords = $feature['geometry']['coordinates'] ?? null;

                $gisid = $feature['properties']['GIS_ID'] ??
                    $feature['properties']['gisid'] ??
                    $feature['properties']['GisId'] ??
                    uniqid('LINE_');

                $roadName = $feature['properties']['road_name'] ??
                    $feature['properties']['Road_Name'] ??
                    $feature['properties']['name'] ??
                    $feature['properties']['NAME'] ??
                    null;

                if (in_array($gisid, $processedGisIds)) {
                    $duplicateGisIds[] = $gisid;
                    $skippedFeatures++;
                    continue;
                }

                if ($mode === 'update') {
                    $existingRecord = DB::table($lineTable)->where('gisid', $gisid)->first();
                    $action = $existingRecord ? 'update' : 'create';
                } else {
                    $existingRecord = DB::table($lineTable)->where('gisid', $gisid)->first();
                    if ($existingRecord) {
                        $duplicateGisIds[] = $gisid;
                        $skippedFeatures++;
                        continue;
                    }
                    $action = 'create';
                }

                $processedGisIds[] = $gisid;
                $featuresToProcess[] = [
                    'index' => $index,
                    'gisid' => $gisid,
                    'geometry_type' => $geometryType,
                    'coordinates' => $coords,
                    'road_name' => $roadName,
                    'action' => $action
                ];
            }

            if (count($featuresToProcess) === 0) {
                return [
                    'success' => false,
                    'message' => 'All line features were skipped due to duplicate GIS_IDs.',
                    'total_features' => count($geoData['features']),
                    'skipped_features' => $skippedFeatures,
                    'duplicate_gisids' => array_unique($duplicateGisIds)
                ];
            }

            $successfulFeatures = 0;
            $failedFeatures = 0;

            DB::beginTransaction();

            try {
                foreach ($featuresToProcess as $featureIndex => $feature) {
                    try {
                        $gisid = $feature['gisid'];
                        $geometryType = $feature['geometry_type'];
                        $coords = $feature['coordinates'];
                        $roadName = $feature['road_name'];
                        $action = $feature['action'];

                        if (!$coords) {
                            $failedFeatures++;
                            continue;
                        }

                        if (!in_array($geometryType, ['LineString', 'MultiLineString'])) {
                            $failedFeatures++;
                            continue;
                        }

                        $lineData = [
                            'gisid' => $gisid,
                            'type' => $geometryType,
                            'road_name' => $roadName,
                            'coordinates' => json_encode($coords, JSON_UNESCAPED_UNICODE),
                            'updated_at' => now()
                        ];

                        if ($action === 'create') {
                            $lineData['created_at'] = now();
                            DB::table($lineTable)->insert($lineData);
                        } else {
                            DB::table($lineTable)
                                ->where('gisid', $gisid)
                                ->update($lineData);
                        }

                        $successfulFeatures++;
                    } catch (\Exception $e) {
                        $failedFeatures++;
                        Log::error("❌ Failed to process line feature: " . $e->getMessage());
                    }
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

            $result = [
                'success' => true,
                'message' => 'Line GeoJSON data processed successfully',
                'total_features' => count($geoData['features']),
                'processed_features' => count($featuresToProcess),
                'successful_features' => $successfulFeatures,
                'skipped_features' => $skippedFeatures,
                'failed_features' => $failedFeatures,
                'duplicate_gisids' => array_unique($duplicateGisIds),
                'mode' => $mode
            ];

            Log::info("✅ Line data processing completed: " . json_encode($result));
            return $result;
        } catch (\Exception $e) {
            Log::error("❌ Failed to process Line GeoJSON: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to process Line GeoJSON: ' . $e->getMessage()
            ];
        }
    }

    /**
     * ✅ Store Single Polygon
     */
    public function storeSinglePolygon($polygonTable, $pointTable, $geoJsonContent)
    {
        try {
            $geometryType = $geoJsonContent['type'] ?? null;
            $coords = $geoJsonContent['coordinates'] ?? null;

            if (!in_array($geometryType, ['Polygon', 'MultiPolygon'])) {
                return [
                    'success' => false,
                    'message' => 'Invalid geometry type. Expected Polygon or MultiPolygon',
                ];
            }

            if (is_string($coords)) {
                $coords = json_decode($coords, true);
                if ($coords === null) {
                    return [
                        'success' => false,
                        'message' => 'Invalid coordinates JSON',
                    ];
                }
            }

            if (empty($coords)) {
                return [
                    'success' => false,
                    'message' => 'Coordinates are empty',
                ];
            }

            // Calculate square footage directly in square feet
            $sqfeet = $this->calculatePolygonAreaInSquareFeet($coords);

            Log::info("Calculated area for new polygon: {$sqfeet} sq ft");

            // Determine new GIS_ID
            $allIds = DB::table($polygonTable)->pluck('gisid');
            $maxNumber = 0;
            $prefix = 'GIS_';

            foreach ($allIds as $id) {
                if (preg_match_all('/\d+/', $id, $matches)) {
                    $numbers = $matches[0];
                    $lastNum = (int)end($numbers);
                    if ($lastNum > $maxNumber) {
                        $maxNumber = $lastNum;
                        $pos = strrpos($id, (string)$lastNum);
                        if ($pos !== false) {
                            $prefix = substr($id, 0, $pos);
                        }
                    }
                }
            }

            $newGisNumber = $maxNumber + 1;
            $gisid = $prefix . $newGisNumber;

            $exists = DB::table($polygonTable)->where('gisid', $gisid)->exists();

            if ($exists) {
                return [
                    'success' => false,
                    'message' => 'GIS ID already exists: ' . $gisid,
                ];
            }

            DB::beginTransaction();

            try {
                DB::table($polygonTable)->insert([
                    'gisid' => $gisid,
                    'type' => $geometryType,
                    'coordinates' => json_encode($coords, JSON_NUMERIC_CHECK),
                    'sqfeet' => (string)$sqfeet,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $flattened = $this->flattenCoordinates($geometryType, $coords);
                $midpoint = $this->calculateMidpoint($flattened);

                if ($midpoint && is_array($midpoint) && count($midpoint) >= 2) {
                    DB::table($pointTable)->insert([
                        'gisid' => $gisid,
                        'type' => 'Point',
                        'coordinates' => json_encode($midpoint, JSON_NUMERIC_CHECK),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    DB::rollBack();
                    return [
                        'success' => false,
                        'message' => 'Failed to calculate midpoint for polygon',
                    ];
                }

                DB::commit();

                return [
                    'success' => true,
                    'message' => 'Polygon inserted successfully',
                    'gisid' => $gisid,
                    'sqfeet' => $sqfeet,
                    'midpoint' => $midpoint
                ];

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error("❌ Failed to store polygon: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to store polygon: ' . $e->getMessage()
            ];
        }
    }

    /**
     * ✅ Update Single Polygon
     */
    public function updateSinglePolygon($polygonTable, $pointTable, $geoJsonContent)
    {
        try {
            $gisid = $geoJsonContent['properties']['GIS_ID']
                ?? $geoJsonContent['properties']['gisid']
                ?? $geoJsonContent['gisid']
                ?? $geoJsonContent['id']
                ?? null;

            $geometryType = $geoJsonContent['type'] ?? null;
            $coords = $geoJsonContent['coordinates'] ?? null;

            if (!in_array($geometryType, ['Polygon', 'MultiPolygon'])) {
                return [
                    'success' => false,
                    'message' => 'Invalid geometry type',
                ];
            }

            if (is_string($coords)) {
                $coords = json_decode($coords, true);
                if ($coords === null) {
                    return [
                        'success' => false,
                        'message' => 'Invalid coordinates JSON'
                    ];
                }
            }

            if (empty($coords)) {
                return [
                    'success' => false,
                    'message' => 'Coordinates are empty',
                ];
            }

            if (!$gisid) {
                return [
                    'success' => false,
                    'message' => 'GIS_ID missing in request'
                ];
            }

            $exists = DB::table($polygonTable)->where('gisid', $gisid)->exists();

            if (!$exists) {
                return [
                    'success' => false,
                    'message' => 'Record not found for GIS_ID: ' . $gisid
                ];
            }

            // Recalculate square footage
            $sqfeet = $this->calculatePolygonAreaInSquareFeet($coords);

            Log::info("Recalculated area for polygon {$gisid}: {$sqfeet} sq ft");

            DB::beginTransaction();

            try {
                DB::table($polygonTable)
                    ->where('gisid', $gisid)
                    ->update([
                        'type' => $geometryType,
                        'coordinates' => json_encode($coords, JSON_NUMERIC_CHECK),
                        'sqfeet' => (string)$sqfeet,
                        'updated_at' => now(),
                    ]);

                $flattened = $this->flattenCoordinates($geometryType, $coords);
                $midpoint = $this->calculateMidpoint($flattened);

                if ($midpoint && is_array($midpoint) && count($midpoint) >= 2) {
                    $pointExists = DB::table($pointTable)->where('gisid', $gisid)->exists();

                    if ($pointExists) {
                        DB::table($pointTable)
                            ->where('gisid', $gisid)
                            ->update([
                                'type' => 'Point',
                                'coordinates' => json_encode($midpoint, JSON_NUMERIC_CHECK),
                                'updated_at' => now(),
                            ]);
                    } else {
                        DB::table($pointTable)->insert([
                            'gisid' => $gisid,
                            'type' => 'Point',
                            'coordinates' => json_encode($midpoint, JSON_NUMERIC_CHECK),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }

                DB::commit();

                return [
                    'success' => true,
                    'message' => 'Polygon updated successfully',
                    'gisid' => $gisid,
                    'sqfeet' => $sqfeet,
                    'midpoint' => $midpoint
                ];

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error("❌ Failed to update polygon: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to update polygon: ' . $e->getMessage()
            ];
        }
    }

    /**
     * ✅ Flatten coordinates
     */
    private function flattenCoordinates($geometryType, $coordinates)
    {
        $flattened = [];

        try {
            if ($geometryType === 'Polygon') {
                foreach ($coordinates as $ring) {
                    if (is_array($ring) && count($ring) > 0) {
                        $flattened[] = $ring;
                    }
                }
            } elseif ($geometryType === 'MultiPolygon') {
                foreach ($coordinates as $polygon) {
                    foreach ($polygon as $ring) {
                        if (is_array($ring) && count($ring) > 0) {
                            $flattened[] = $ring;
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error("❌ Failed to flatten coordinates: " . $e->getMessage());
        }

        return $flattened;
    }

    /**
     * ✅ Calculate midpoint
     */
    private function calculateMidpoint($flattened)
    {
        if (empty($flattened) || !is_array($flattened[0])) {
            return null;
        }

        try {
            $points = $flattened[0];
            $count = count($points);

            if ($count === 0) {
                return null;
            }

            $totalX = 0;
            $totalY = 0;

            foreach ($points as $point) {
                if (is_array($point) && count($point) >= 2) {
                    $totalX += floatval($point[0]);
                    $totalY += floatval($point[1]);
                }
            }

            $centroidX = $totalX / $count;
            $centroidY = $totalY / $count;

            return [$centroidX, $centroidY];

        } catch (\Exception $e) {
            Log::error("❌ Failed to calculate midpoint: " . $e->getMessage());
            return null;
        }
    }

    /**
     * ✅ Get polygon by GIS_ID
     */
    public function getPolygonByGisId($polygonTable, $gisid)
    {
        try {
            return DB::table($polygonTable)->where('gisid', $gisid)->first();
        } catch (\Exception $e) {
            Log::error("❌ Failed to get polygon: " . $e->getMessage());
            return null;
        }
    }

    /**
     * ✅ Get point by GIS_ID
     */
    public function getPointByGisId($pointTable, $gisid)
    {
        try {
            return DB::table($pointTable)->where('gisid', $gisid)->first();
        } catch (\Exception $e) {
            Log::error("❌ Failed to get point: " . $e->getMessage());
            return null;
        }
    }

    /**
     * ✅ Get line by GIS_ID
     */
    public function getLineByGisId($lineTable, $gisid)
    {
        try {
            return DB::table($lineTable)->where('gisid', $gisid)->first();
        } catch (\Exception $e) {
            Log::error("❌ Failed to get line: " . $e->getMessage());
            return null;
        }
    }

    /**
     * ✅ Delete feature by GIS_ID
     */
    public function deleteFeatureByGisId($polygonTable, $pointTable, $gisid)
    {
        try {
            $polygonDeleted = DB::table($polygonTable)->where('gisid', $gisid)->delete();
            $pointDeleted = DB::table($pointTable)->where('gisid', $gisid)->delete();

            if ($polygonDeleted || $pointDeleted) {
                return [
                    'success' => true,
                    'message' => 'Feature deleted successfully.',
                ];
            }

            return [
                'success' => false,
                'message' => 'No matching feature found.',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ];
        }
    }

    /** ✅ Table name generators */
    public function generatePolygonTableName($corporationId, $zone, $wardNumber)
    {
        return 'polygon_' . $corporationId . '_' . $this->sanitize($zone) . '_' . $this->sanitize($wardNumber);
    }

    public function generatePointTableName($corporationId, $zone, $wardNumber)
    {
        return 'point_' . $corporationId . '_' . $this->sanitize($zone) . '_' . $this->sanitize($wardNumber);
    }

    public function generateLineTableName($corporationId, $zone, $wardNumber)
    {
        return 'line_' . $corporationId . '_' . $this->sanitize($zone) . '_' . $this->sanitize($wardNumber);
    }

    public function generatePointDataTableName($corporationId, $zone, $wardNumber)
    {
        return 'pointdata_' . $corporationId . '_' . $this->sanitize($zone) . '_' . $this->sanitize($wardNumber);
    }

    public function generatePolygonDataTableName($corporationId, $zone, $wardNumber)
    {
        return 'polygondata_' . $corporationId . '_' . $this->sanitize($zone) . '_' . $this->sanitize($wardNumber);
    }

    public function generateShopDataTableName($corporationId, $zone, $wardNumber)
    {
        return 'shopdata_' . $corporationId . '_' . $this->sanitize($zone) . '_' . $this->sanitize($wardNumber);
    }


    private function sanitize($string)
    {
        return preg_replace('/[^a-zA-Z0-9_]/', '_', strtolower($string));
    }
}

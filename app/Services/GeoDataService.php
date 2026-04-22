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

            // ✅ Create Polygon Data Table FIRST (this must exist before point data table)
            if (!Schema::hasTable($polygonDataTable)) {
                Schema::create($polygonDataTable, function (Blueprint $table) {
                    $table->id();
                    $table->string('gisid')->nullable();
                    $table->string('number_bill')->nullable();
                    $table->string('number_shop')->nullable();
                    $table->string('number_floor')->nullable();
                    $table->string('new_address')->nullable();
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
                    $table->string('sqfeet')->nullable();
                    $table->string('merge')->nullable();
                    $table->string('split')->nullable();
                    $table->string('worker_name')->nullable();
                    $table->string('remarks')->nullable();
                    $table->string('corporationremarks')->nullable();
                    $table->timestamps();
                    $table->softDeletes();
                });
                Log::info("✅ Polygon data table created: {$polygonDataTable}");
            }

            // ✅ Create Point Data Table SECOND (with foreign key to polygon data table)
            if (!Schema::hasTable($pointDataTable)) {
                Schema::create($pointDataTable, function (Blueprint $table) use ($polygonDataTable) {
                    $table->id();

                    // Foreign key - defined only once
                    $table->unsignedBigInteger('building_data_id')->nullable();
                    $table->foreign('building_data_id')
                        ->references('id')
                        ->on($polygonDataTable)
                        ->onUpdate('cascade')
                        ->onDelete('cascade');

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
                    $table->string('shop_floor')->nullable();
                    $table->string('shop_name')->nullable();
                    $table->string('shop_owner_name')->nullable();
                    $table->string('old_door_no')->nullable();
                    $table->string('new_door_no')->nullable();
                    $table->string('shop_category')->nullable();
                    $table->string('shop_mobile')->nullable();
                    $table->string('license')->nullable();
                    $table->string('professional_tax')->nullable();
                    $table->string('gst')->nullable();
                    $table->string('number_of_employee')->nullable();
                    $table->string('trade_income')->nullable();
                    $table->string('establishment_remarks')->nullable();
                    $table->string('remarks')->nullable();
                    $table->string('plot_area')->nullable();
                    $table->string('water_tax')->nullable();
                    $table->string('halfyeartax')->nullable();
                    $table->string('balance')->nullable();
                    // REMOVED: $table->string('building_data_id')->nullable(); // This was the duplicate
                    $table->string('qc_area')->nullable();
                    $table->string('qc_usage')->nullable();
                    $table->string('qc_name')->nullable();
                    $table->string('qc_remarks')->nullable();
                    $table->string('otsarea')->nullable();
                    $table->string('zone')->nullable();
                    $table->timestamps();
                    $table->softDeletes();
                });

                Log::info("✅ Point data table created: {$pointDataTable}");
            }
            if (!Schema::hasTable($shopDataTable)) {
                Schema::create($shopDataTable, function (Blueprint $table) use ($pointDataTable) {
                    $table->id();

                    // Foreign key (relation with point table)
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
     * ✅ Store polygons & points from GeoJSON with GIS_ID validation
     * Optimized for performance with large files
     */
    public function storePolygonData($polygonTable, $pointTable, $geoJsonContent, $mode = 'create')
    {
        // Increase execution time for large files
        set_time_limit(600); // 10 minutes

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

            // First pass: Identify unique GIS_IDs and prepare features
            foreach ($geoData['features'] as $index => $feature) {
                // Progress logging for large files
                if ($index % 100 === 0) {
                    Log::info("🔍 Processing polygon feature {$index} of " . count($geoData['features']));
                }

                $geometryType = $feature['geometry']['type'] ?? null;
                $coords = $feature['geometry']['coordinates'] ?? null;

                // Extract GIS_ID or generate unique one
                $gisid = $feature['properties']['GIS_ID'] ??
                    $feature['properties']['gisid'] ??
                    $feature['properties']['GisId'] ??
                    uniqid('GIS_');

                // Skip if GIS_ID is already processed in this upload (duplicate in file)
                if (in_array($gisid, $processedGisIds)) {
                    $duplicateGisIds[] = $gisid;
                    $skippedFeatures++;
                    continue;
                }

                // For update mode: Check if GIS_ID already exists in database
                if ($mode === 'update') {
                    $existingRecord = DB::table($polygonTable)->where('gisid', $gisid)->first();
                    $action = $existingRecord ? 'update' : 'create';
                } else {
                    // For create mode: Check if GIS_ID already exists in database
                    $existingRecord = DB::table($polygonTable)->where('gisid', $gisid)->first();
                    if ($existingRecord) {
                        // Skip this feature but continue with others
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

            // If all features are skipped due to duplicates
            if (count($featuresToProcess) === 0) {
                return [
                    'success' => false,
                    'message' => 'All polygon features were skipped due to duplicate GIS_IDs. No data was processed.',
                    'total_features' => count($geoData['features']),
                    'skipped_features' => $skippedFeatures,
                    'duplicate_gisids' => array_unique($duplicateGisIds)
                ];
            }

            $successfulFeatures = 0;
            $failedFeatures = 0;

            // Use transaction for better performance
            DB::beginTransaction();

            try {
                // Second pass: Process unique features (insert or update)
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

                        // 🔹 Flatten polygon/multipolygon
                        $flattened = $this->flattenCoordinates($geometryType, $coords);

                        if (empty($flattened)) {
                            $failedFeatures++;
                            continue;
                        }

                        // ✅ Store/Update in polygon table
                        $polygonData = [
                            'type' => 'Polygon',
                            'coordinates' => json_encode($flattened, JSON_UNESCAPED_UNICODE),
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

                        // ✅ Calculate midpoint for points table (simplified for performance)
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

                        // Progress logging
                        if ($featureIndex % 50 === 0) {
                            Log::info("📝 Processed polygon feature {$featureIndex} of " . count($featuresToProcess) . " features");
                        }
                    } catch (\Exception $e) {
                        $failedFeatures++;
                        Log::error("❌ Failed to process polygon feature with GIS_ID {$gisid}: " . $e->getMessage());
                        // Continue with next feature even if this one fails
                    }
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

            $result = [
                'success' => true,
                'message' => 'Polygon GeoJSON data processed successfully with some duplicates omitted',
                'total_features' => count($geoData['features']),
                'processed_features' => count($featuresToProcess),
                'successful_features' => $successfulFeatures,
                'skipped_features' => $skippedFeatures,
                'failed_features' => $failedFeatures,
                'duplicate_gisids' => array_unique($duplicateGisIds),
                'mode' => $mode
            ];

            Log::info("✅ Polygon & point data processing completed: " . json_encode($result));
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
     * ✅ Store line data from GeoJSON with GIS_ID validation
     */
    public function storeLineData($lineTable, $geoJsonContent, $mode = 'create')
    {
        // Increase execution time for large files
        set_time_limit(600); // 10 minutes

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

            // First pass: Identify unique GIS_IDs and prepare features
            foreach ($geoData['features'] as $index => $feature) {
                // Progress logging for large files
                if ($index % 100 === 0) {
                    Log::info("🔍 Processing line feature {$index} of " . count($geoData['features']));
                }

                $geometryType = $feature['geometry']['type'] ?? null;
                $coords = $feature['geometry']['coordinates'] ?? null;

                // Extract GIS_ID or generate unique one
                $gisid = $feature['properties']['GIS_ID'] ??
                    $feature['properties']['gisid'] ??
                    $feature['properties']['GisId'] ??
                    uniqid('LINE_');

                // Extract road_name from properties
                $roadName = $feature['properties']['road_name'] ??
                    $feature['properties']['Road_Name'] ??
                    $feature['properties']['name'] ??
                    $feature['properties']['NAME'] ??
                    null;

                // Skip if GIS_ID is already processed in this upload (duplicate in file)
                if (in_array($gisid, $processedGisIds)) {
                    $duplicateGisIds[] = $gisid;
                    $skippedFeatures++;
                    continue;
                }

                // For update mode: Check if GIS_ID already exists in database
                if ($mode === 'update') {
                    $existingRecord = DB::table($lineTable)->where('gisid', $gisid)->first();
                    $action = $existingRecord ? 'update' : 'create';
                } else {
                    // For create mode: Check if GIS_ID already exists in database
                    $existingRecord = DB::table($lineTable)->where('gisid', $gisid)->first();
                    if ($existingRecord) {
                        // Skip this feature but continue with others
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

            // If all features are skipped due to duplicates
            if (count($featuresToProcess) === 0) {
                return [
                    'success' => false,
                    'message' => 'All line features were skipped due to duplicate GIS_IDs. No data was processed.',
                    'total_features' => count($geoData['features']),
                    'skipped_features' => $skippedFeatures,
                    'duplicate_gisids' => array_unique($duplicateGisIds)
                ];
            }

            $successfulFeatures = 0;
            $failedFeatures = 0;

            // Use transaction for better performance
            DB::beginTransaction();

            try {
                // Second pass: Process unique features (insert or update)
                foreach ($featuresToProcess as $featureIndex => $feature) {
                    try {
                        $gisid = $feature['gisid'];
                        $geometryType = $feature['geometry_type'];
                        $coords = $feature['coordinates'];
                        $roadName = $feature['road_name'];
                        $action = $feature['action'];

                        if (!$coords) {
                            $failedFeatures++;
                            Log::warning("❌ No coordinates found for line feature with GIS_ID: {$gisid}");
                            continue;
                        }

                        // Validate it's actually a line geometry
                        if (!in_array($geometryType, ['LineString', 'MultiLineString'])) {
                            $failedFeatures++;
                            Log::warning("❌ Invalid geometry type for line: {$geometryType} for GIS_ID: {$gisid}");
                            continue;
                        }

                        // ✅ Store/Update in line table
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

                        // Progress logging
                        if ($featureIndex % 50 === 0) {
                            Log::info("📝 Processed line feature {$featureIndex} of " . count($featuresToProcess) . " features");
                        }
                    } catch (\Exception $e) {
                        $failedFeatures++;
                        Log::error("❌ Failed to process line feature with GIS_ID {$gisid}: " . $e->getMessage());
                        // Continue with next feature even if this one fails
                    }
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

            $result = [
                'success' => true,
                'message' => 'Line GeoJSON data processed successfully with some duplicates omitted',
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
     * ✅ Flatten coordinates for different geometry types
     */
    private function flattenCoordinates($geometryType, $coordinates)
    {
        $flattened = [];

        try {
            if ($geometryType === 'Polygon') {
                foreach ($coordinates as $ring) {
                    $flattened[] = $ring;
                }
            } elseif ($geometryType === 'MultiPolygon') {
                foreach ($coordinates as $polygon) {
                    foreach ($polygon as $ring) {
                        $flattened[] = $ring;
                    }
                }
            }
            // Add support for other geometry types if needed
        } catch (\Exception $e) {
            Log::error("❌ Failed to flatten coordinates: " . $e->getMessage());
        }

        return $flattened;
    }

    /**
     * ✅ Calculate midpoint (optimized for performance)
     */
    private function calculateMidpoint($flattened)
    {
        if (empty($flattened) || !is_array($flattened[0])) {
            return null;
        }

        try {
            $points = $flattened[0]; // first ring
            $count = count($points);

            if ($count === 0) return null;

            // For performance, sample points if there are too many
            $samplePoints = $count > 1000 ? array_rand($points, 1000) : $points;

            $totalX = $totalY = 0;
            $validPoints = 0;

            foreach ((array)$samplePoints as $point) {
                if (is_array($point) && count($point) >= 2) {
                    $totalX += $point[0];
                    $totalY += $point[1];
                    $validPoints++;
                }
            }

            return $validPoints > 0 ? [$totalX / $validPoints, $totalY / $validPoints] : null;
        } catch (\Exception $e) {
            Log::error("❌ Failed to calculate midpoint: " . $e->getMessage());
            return null;
        }
    }

    /**
     * ✅ Get polygon data by GIS_ID
     */
    public function getPolygonByGisId($polygonTable, $gisid)
    {
        try {
            return DB::table($polygonTable)->where('gisid', $gisid)->first();
        } catch (\Exception $e) {
            Log::error("❌ Failed to get polygon by GIS_ID: " . $e->getMessage());
            return null;
        }
    }

    /**
     * ✅ Get point data by GIS_ID
     */
    public function getPointByGisId($pointTable, $gisid)
    {
        try {
            return DB::table($pointTable)->where('gisid', $gisid)->first();
        } catch (\Exception $e) {
            Log::error("❌ Failed to get point by GIS_ID: " . $e->getMessage());
            return null;
        }
    }

    /**
     * ✅ Get line data by GIS_ID
     */
    public function getLineByGisId($lineTable, $gisid)
    {
        try {
            return DB::table($lineTable)->where('gisid', $gisid)->first();
        } catch (\Exception $e) {
            Log::error("❌ Failed to get line by GIS_ID: " . $e->getMessage());
            return null;
        }
    }

    /** ✅ Public table name generators */
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
    public function storeSinglePolygon($polygonTable, $pointTable, $geoJsonContent)
    {
        // Extract geometry
        $geometryType = $geoJsonContent['type'] ?? null;
        $coords = $geoJsonContent['coordinates'] ?? null;

        // Decode coordinates if string
        if (is_string($coords)) {
            $coords = json_decode($coords, true);
            if ($coords === null) {
                return [
                    'success' => false,
                    'message' => 'Invalid coordinates JSON',
                ];
            }
        }

        // -------------------------------
        // Determine new GIS_ID based on last numeric portion
        // -------------------------------
        $allIds = DB::table($polygonTable)->pluck('gisid');
        $maxNumber = 0;
        $prefix = '';

        foreach ($allIds as $id) {
            if (preg_match_all('/\d+/', $id, $matches)) {
                $numbers = $matches[0];
                $lastNum = (int)end($numbers); // last numeric part
                if ($lastNum > $maxNumber) {
                    $maxNumber = $lastNum;
                    // Preserve prefix: everything before last numeric part
                    $prefix = substr($id, 0, strrpos($id, (string)$lastNum));
                }
            }
        }

        $newGisNumber = $maxNumber + 1;
        $gisid = $prefix . $newGisNumber; // final GIS_ID to insert

        // -------------------------------
        // Check duplicate just in case
        // -------------------------------
        $exists = DB::table($polygonTable)->where('gisid', $gisid)->exists();

        if (!$exists) {
            // Insert polygon
            DB::table($polygonTable)->insert([
                'gisid' => $gisid,
                'type' => $geometryType,
                'coordinates' => json_encode($coords, JSON_NUMERIC_CHECK),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Flatten coordinates and calculate midpoint
            $flattened = $this->flattenCoordinates($geometryType, $coords);
            $midpoint = $this->calculateMidpoint($flattened);

            if ($midpoint) {
                DB::table($pointTable)->insert([
                    'gisid' => $gisid,
                    'type' => 'Point',
                    'coordinates' => json_encode($midpoint, JSON_NUMERIC_CHECK),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return [
            'success' => true,
            'message' => 'Polygon inserted successfully',
            'gisid' => $gisid
        ];
    }
    public function updateSinglePolygon($polygonTable, $pointTable, $geoJsonContent)
    {
        $gisid = $geoJsonContent['properties']['GIS_ID']
            ?? $geoJsonContent['properties']['gisid']
            ?? $geoJsonContent['gisid']
            ?? null;

        $geometryType = $geoJsonContent['type'] ?? null;
        $coords = $geoJsonContent['coordinates'] ?? null;

        if (is_string($coords)) {
            $coords = json_decode($coords, true);
            if ($coords === null) {
                return ['success' => false, 'message' => 'Invalid coordinates JSON'];
            }
        }

        if (!$gisid) {
            return ['success' => false, 'message' => 'GIS_ID missing in request'];
        }

        $exists = DB::table($polygonTable)->where('gisid', $gisid)->exists();

        if (!$exists) {
            return ['success' => false, 'message' => 'Record not found for GIS_ID: ' . $gisid];
        }

        DB::table($polygonTable)
            ->where('gisid', $gisid)
            ->update([
                'type' => $geometryType,
                'coordinates' => json_encode($coords, JSON_NUMERIC_CHECK),
                'updated_at' => now(),
            ]);

        $flattened = $this->flattenCoordinates($geometryType, $coords);
        $midpoint = $this->calculateMidpoint($flattened);

        if ($midpoint) {
            DB::table($pointTable)
                ->where('gisid', $gisid)
                ->update([
                    'type' => 'Point',
                    'coordinates' => json_encode($midpoint, JSON_NUMERIC_CHECK),
                    'updated_at' => now(),
                ]);
        }

        return [
            'success' => true,
            'message' => 'Polygon updated successfully',
            'gisid' => $gisid
        ];
    }
    public function deleteFeatureByGisId($polygonTable, $pointTable, $gisid)
    {
        try {
            // Delete from Polygon Table
            $polygonDeleted = DB::table($polygonTable)
                ->where('gisid', $gisid)
                ->delete();

            // Delete from Point Table
            $pointDeleted = DB::table($pointTable)
                ->where('gisid', $gisid)
                ->delete();

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
}

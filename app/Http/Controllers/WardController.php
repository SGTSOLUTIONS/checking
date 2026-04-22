<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Ward;
use App\Models\Corporation;
use App\Services\GeoDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class WardController extends Controller
{
    protected $geoDataService;

    public function __construct(GeoDataService $geoDataService)
    {
        $this->geoDataService = $geoDataService;
    }

    public function wards($corporationId)
    {
        $corporation = Corporation::findOrFail($corporationId);
        return view('admin.wards', [
            'corporationId' => $corporationId,
            'corporation' => $corporation
        ]);
    }

    public function index($corporationId)
    {
        try {
            $corporation = Corporation::findOrFail($corporationId);

            $wards = Ward::where('corporation_id', $corporationId)->get();

            $tableName = "mis_corporation_{$corporation->id}";

            $misData = [];

            if (Schema::hasTable($tableName)) {
                $misData = DB::table($tableName)
                    ->select('ward_no', DB::raw('GROUP_CONCAT(DISTINCT road_name) as roads'))
                    ->groupBy('ward_no')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'ward_no' => $item->ward_no,
                            'roads' => $item->roads ? explode(',', $item->roads) : []
                        ];
                    });
            }

            // Attach MIS data to each ward
            $wards->transform(function ($ward) use ($misData) {
                if ($ward->drone_image) {
                    $ward->drone_image_url = asset($ward->drone_image);
                }

                // Match ward_no and get roads
                $matchedData = $misData->firstWhere('ward_no', $ward->ward_no);
                $ward->roads = $matchedData ? $matchedData['roads'] : [];
                $ward->roads_count = count($ward->roads);

                return $ward;
            });

            return response()->json([
                'success' => true,
                'corporation' => $corporation,
                'wards' => $wards
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to load ward data: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to load ward data'
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $ward = Ward::findOrFail($id);

            // Add full URL for drone_image
            if ($ward->drone_image) {
                $ward->drone_image_url = asset($ward->drone_image);
            }

            return response()->json([
                'success' => true,
                'ward' => $ward
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to fetch ward details: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch ward details'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'corporation_id' => 'required|exists:corporations,id',
            'ward_no' => 'required|string|max:255',
            'zone' => 'required|string|max:255',
            'drone_image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'extent_left' => 'nullable|numeric',
            'extent_right' => 'nullable|numeric',
            'extent_top' => 'nullable|numeric',
            'extent_bottom' => 'nullable|numeric',
            'boundary' => 'nullable|file',
            'polygon' => 'nullable|file',
            'line' => 'nullable|file',
            'status' => 'required|in:active,inactive'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->all();
        $zone = strtolower(trim($request->zone));   // South → south
        $zone = preg_replace('/\s+/', '_', $zone);
        $data['zone'] =  $zone;
        $uploadedFiles = [];

        try {
            // Prevent duplicate ward no
            $exists = Ward::where('corporation_id', $request->corporation_id)
                ->where('ward_no', $request->ward_no)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ward number already exists for this corporation.'
                ], 422);
            }
            // Handle drone image - store directly in public path
            if ($request->hasFile('drone_image')) {
                $uploadDir = 'uploads/wards/drone_images';
                $fullPath = public_path($uploadDir);

                // Create directory if it doesn't exist
                if (!File::exists($fullPath)) {
                    File::makeDirectory($fullPath, 0755, true);
                }

                $fileName = time() . '_' . uniqid() . '.' . $request->file('drone_image')->getClientOriginalExtension();
                $request->file('drone_image')->move($fullPath, $fileName);

                $data['drone_image'] = $uploadDir . '/' . $fileName;
                $uploadedFiles[] = $data['drone_image'];

                // Add URL to response
                $data['drone_image_url'] = asset($data['drone_image']);
            }

            // Handle boundary file — only store coordinates
            if ($request->hasFile('boundary')) {
                $geojsonContent = file_get_contents($request->file('boundary')->getRealPath());
                $boundaryData = json_decode($geojsonContent, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    $this->cleanupFiles($uploadedFiles);
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid GeoJSON format in boundary file.'
                    ], 422);
                }

                $coordinates = null;
                if (isset($boundaryData['features'][0]['geometry']['coordinates'])) {
                    $coordinates = $boundaryData['features'][0]['geometry']['coordinates'];
                } elseif (isset($boundaryData['geometry']['coordinates'])) {
                    $coordinates = $boundaryData['geometry']['coordinates'];
                }

                if (!$coordinates) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No valid coordinates found in boundary file.'
                    ], 422);
                }

                $data['boundary'] = $coordinates[0];
            }

            $polygonProcessResult = null;
            $lineProcessResult = null;

            // Handle polygon file - process directly without storing
            if ($request->hasFile('polygon')) {
                $polygonFile = $request->file('polygon');
                $polygonContent = file_get_contents($polygonFile->getRealPath());
                $polygonData = json_decode($polygonContent, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    $this->cleanupFiles($uploadedFiles);
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid GeoJSON format in polygon file.'
                    ], 422);
                }

                // Create both polygon and point tables
                $tables = $this->geoDataService->createWardTables(
                    $request->corporation_id,
                    $request->zone,
                    $request->ward_no
                );

                $polygonProcessResult = $this->geoDataService->storePolygonData(
                    $tables['polygon_table'],
                    $tables['point_table'],
                    $polygonContent,
                    'create'
                );

                if (!$polygonProcessResult['success'] && $polygonProcessResult['successful_features'] === 0) {
                    $this->cleanupFiles($uploadedFiles);
                    return response()->json([
                        'success' => false,
                        'message' => $polygonProcessResult['message'],
                        'details' => $polygonProcessResult
                    ], 422);
                }

                $data['polygon'] = null;
            }

            // Handle line file
            if ($request->hasFile('line')) {
                $lineFile = $request->file('line');
                $lineContent = file_get_contents($lineFile->getRealPath());
                $lineData = json_decode($lineContent, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    $this->cleanupFiles($uploadedFiles);
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid GeoJSON format in line file.'
                    ], 422);
                }

                // Create line table
                $lineTable = $this->geoDataService->createLineTables(
                    $request->corporation_id,
                    $request->zone,
                    $request->ward_no
                );

                $lineProcessResult = $this->geoDataService->storeLineData(
                    $lineTable,
                    $lineContent,
                    'create'
                );

                if (!$lineProcessResult['success'] && $lineProcessResult['successful_features'] === 0) {
                    $this->cleanupFiles($uploadedFiles);
                    return response()->json([
                        'success' => false,
                        'message' => $lineProcessResult['message'],
                        'details' => $lineProcessResult
                    ], 422);
                }

                $data['line'] = null;
            }

            $ward = Ward::create($data);

            // Add URL to the created ward
            if ($ward->drone_image) {
                $ward->drone_image_url = asset($ward->drone_image);
            }

            $response = [
                'success' => true,
                'message' => 'Ward created successfully',
                'ward' => $ward
            ];

            if ($polygonProcessResult) {
                $response['polygon_processing'] = $polygonProcessResult;
                if ($polygonProcessResult['skipped_features'] > 0) {
                    $response['message'] = 'Ward created successfully with ' . $polygonProcessResult['skipped_features'] . ' duplicate polygon GIS_IDs omitted';
                }
            }

            if ($lineProcessResult) {
                $response['line_processing'] = $lineProcessResult;
                if ($lineProcessResult['skipped_features'] > 0) {
                    $response['message'] = ($response['message'] ?? 'Ward created successfully') . ' and ' . $lineProcessResult['skipped_features'] . ' duplicate line GIS_IDs omitted';
                }
            }

            return response()->json($response);
        } catch (\Exception $e) {
            $this->cleanupFiles($uploadedFiles);
            Log::error("Failed to create ward: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create ward: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'ward_no' => 'required|string|max:255',
            'zone' => 'required|string|max:255',
            'drone_image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'extent_left' => 'nullable|numeric',
            'extent_right' => 'nullable|numeric',
            'extent_top' => 'nullable|numeric',
            'extent_bottom' => 'nullable|numeric',
            'boundary' => 'nullable|file',
            'polygon' => 'nullable|file',
            'status' => 'required|in:active,inactive',
            'line' => 'nullable|file'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $ward = Ward::findOrFail($id);
            $data = $request->all();
            $zone = strtolower(trim($request->zone));
            $zone = preg_replace('/\s+/', '_', $zone);
            $polygonProcessResult = null;
            $lineProcessResult = null;
            $newFiles = [];

            if ($request->hasFile('drone_image')) {
                // Delete old image if exists
                if ($ward->drone_image && File::exists(public_path($ward->drone_image))) {
                    File::delete(public_path($ward->drone_image));
                }

                $uploadDir = 'uploads/wards/drone_images';
                $fullPath = public_path($uploadDir);

                // Create directory if it doesn't exist
                if (!File::exists($fullPath)) {
                    File::makeDirectory($fullPath, 0755, true);
                }

                $fileName = time() . '_' . uniqid() . '.' . $request->file('drone_image')->getClientOriginalExtension();
                $request->file('drone_image')->move($fullPath, $fileName);

                $data['drone_image'] = $uploadDir . '/' . $fileName;
                $newFiles[] = $data['drone_image'];

                // Add URL to response
                $data['drone_image_url'] = asset($data['drone_image']);
            }

            // Handle boundary file update — only store coordinates
            if ($request->hasFile('boundary')) {
                $geojsonContent = file_get_contents($request->file('boundary')->getRealPath());
                $boundaryData = json_decode($geojsonContent, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    $this->cleanupFiles($newFiles);
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid GeoJSON format in boundary file.'
                    ], 422);
                }

                $coordinates = null;
                if (isset($boundaryData['features'][0]['geometry']['coordinates'])) {
                    $coordinates = $boundaryData['features'][0]['geometry']['coordinates'];
                } elseif (isset($boundaryData['geometry']['coordinates'])) {
                    $coordinates = $boundaryData['geometry']['coordinates'];
                }

                if (!$coordinates) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No valid coordinates found in boundary file.'
                    ], 422);
                }

                $data['boundary'] = $coordinates[0];
            }

            // Handle polygon file update
            if ($request->hasFile('polygon')) {
                $polygonFile = $request->file('polygon');
                $polygonContent = file_get_contents($polygonFile->getRealPath());
                $polygonData = json_decode($polygonContent, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    $this->cleanupFiles($newFiles);
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid GeoJSON format in polygon file.'
                    ], 422);
                }

                $polygonTable = $this->geoDataService->generatePolygonTableName(
                    $ward->corporation_id,
                    $request->zone,
                    $request->ward_no
                );
                $pointTable = $this->geoDataService->generatePointTableName(
                    $ward->corporation_id,
                    $request->zone,
                    $request->ward_no
                );

                if (!Schema::hasTable($polygonTable) || !Schema::hasTable($pointTable)) {
                    $this->geoDataService->createWardTables(
                        $ward->corporation_id,
                        $request->zone,
                        $request->ward_no
                    );
                }

                $polygonProcessResult = $this->geoDataService->storePolygonData(
                    $polygonTable,
                    $pointTable,
                    $polygonContent,
                    'update'
                );

                if (!$polygonProcessResult['success'] && $polygonProcessResult['successful_features'] === 0) {
                    $this->cleanupFiles($newFiles);
                    return response()->json([
                        'success' => false,
                        'message' => $polygonProcessResult['message'],
                        'details' => $polygonProcessResult
                    ], 422);
                }

                $data['polygon'] = null;
            }

            // Handle line file update
            if ($request->hasFile('line')) {
                $lineFile = $request->file('line');
                $lineContent = file_get_contents($lineFile->getRealPath());
                $lineData = json_decode($lineContent, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    $this->cleanupFiles($newFiles);
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid GeoJSON format in line file.'
                    ], 422);
                }

                $lineTable = $this->geoDataService->generateLineTableName(
                    $ward->corporation_id,
                    $request->zone,
                    $request->ward_no
                );

                if (!Schema::hasTable($lineTable)) {
                    $this->geoDataService->createLineTables(
                        $ward->corporation_id,
                        $request->zone,
                        $request->ward_no
                    );
                }

                $lineProcessResult = $this->geoDataService->storeLineData(
                    $lineTable,
                    $lineContent,
                    'update'
                );

                if (!$lineProcessResult['success'] && $lineProcessResult['successful_features'] === 0) {
                    $this->cleanupFiles($newFiles);
                    return response()->json([
                        'success' => false,
                        'message' => $lineProcessResult['message'],
                        'details' => $lineProcessResult
                    ], 422);
                }

                $data['line'] = null;
            }

            $ward->update($data);

            // Add URL to the updated ward
            if ($ward->drone_image) {
                $ward->drone_image_url = asset($ward->drone_image);
            }

            $response = [
                'success' => true,
                'message' => 'Ward updated successfully',
                'ward' => $ward
            ];

            if ($polygonProcessResult) {
                $response['polygon_processing'] = $polygonProcessResult;
                if ($polygonProcessResult['skipped_features'] > 0) {
                    $response['message'] = 'Ward updated successfully with ' . $polygonProcessResult['skipped_features'] . ' duplicate polygon GIS_IDs omitted';
                }
            }

            if ($lineProcessResult) {
                $response['line_processing'] = $lineProcessResult;
                if ($lineProcessResult['skipped_features'] > 0) {
                    $response['message'] = ($response['message'] ?? 'Ward updated successfully') . ' and ' . $lineProcessResult['skipped_features'] . ' duplicate line GIS_IDs omitted';
                }
            }

            return response()->json($response);
        } catch (\Exception $e) {
            Log::error("Failed to update ward: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update ward: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $ward = Ward::findOrFail($id);

            // Delete drone image from public path
            if ($ward->drone_image && File::exists(public_path($ward->drone_image))) {
                File::delete(public_path($ward->drone_image));
            }

            $polygonTable = $this->geoDataService->generatePolygonTableName(
                $ward->corporation_id,
                $ward->zone,
                $ward->ward_no
            );
            $pointTable = $this->geoDataService->generatePointTableName(
                $ward->corporation_id,
                $ward->zone,
                $ward->ward_no
            );
            $lineTable = $this->geoDataService->generateLineTableName(
                $ward->corporation_id,
                $ward->zone,
                $ward->ward_no
            );

            if (Schema::hasTable($polygonTable)) {
                Schema::dropIfExists($polygonTable);
            }
            if (Schema::hasTable($pointTable)) {
                Schema::dropIfExists($pointTable);
            }
            if (Schema::hasTable($lineTable)) {
                Schema::dropIfExists($lineTable);
            }

            $ward->delete();

            return response()->json([
                'success' => true,
                'message' => 'Ward deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to delete ward: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete ward: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Download Missing Bill Data
     */
    public function downloadMissingBill($id, Request $request)
    {
        try {
            $ward = Ward::findOrFail($id);
            $roadname = $request->input('roadname');

            if (!$roadname) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select a road name'
                ], 400);
            }

            $tableName = "mis_corporation_{$ward->corporation_id}";

            if (!Schema::hasTable($tableName)) {
                return response()->json([
                    'success' => false,
                    'message' => 'MIS data not found for this corporation'
                ], 404);
            }

            $zone = strtolower(trim($ward->zone));   // easts → south
            $zone = preg_replace('/\s+/', '_', $zone); // handle spaces

            $pointTable = "pointdata_{$ward->corporation_id}_{$zone}_{$ward->ward_no}";

            // Check if point table exists
            if (!Schema::hasTable($pointTable)) {
                // If point table doesn't exist, all MIS records are missing
                return response()->json([
                    'success' => false,
                    'message' => 'Point data not found for this ward. All MIS records are considered missing.',
                    'pointTable' => $pointTable
                ], 404);
                $data = DB::table($tableName)
                    ->where('ward_no', $ward->ward_no)
                    ->where('road_name', $roadname)
                    ->get();
            } else {
                // Use NOT EXISTS for better performance
                $data = DB::table($tableName . ' as mis')
                    ->where('mis.ward_no', $ward->ward_no)
                    ->where('mis.road_name', $roadname)
                    ->whereNotExists(function ($query) use ($pointTable) {
                        $query->select(DB::raw(1))
                            ->from($pointTable . ' as pt')
                            ->whereRaw('pt.assessment = mis.assessment');
                    })
                    ->select('mis.*')
                    ->get();
            }

            if ($data->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No missing bill data found for this ward and road'
                ], 404);
            }

            // Generate CSV file name
            $fileName = "missing_bill_ward_{$ward->ward_no}_road_" . str_replace(' ', '_', $roadname) . "_" . date('Y-m-d') . ".csv";

            $callback = function () use ($data) {
                $file = fopen('php://output', 'w');
                fwrite($file, "\xEF\xBB\xBF");

                if ($data->count() > 0) {
                    $headers = array_keys((array)$data[0]);
                    fputcsv($file, $headers);

                    foreach ($data as $row) {
                        fputcsv($file, (array)$row);
                    }
                }
                fclose($file);
            };

            return response()->stream($callback, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to download missing bill data: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to download missing bill data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download Polygon (Optimized)
     */
    public function downloadPolygon($id)
    {
        try {
            $ward = Ward::findOrFail($id);

            $zone = strtolower(trim($ward->zone));
            $zone = preg_replace('/\s+/', '_', $zone);

            $polygonTable  = "polygon_{$ward->corporation_id}_{$zone}_{$ward->ward_no}";
            $pointTable    = "pointdata_{$ward->corporation_id}_{$zone}_{$ward->ward_no}";
            $buildingTable = "polygondata_{$ward->corporation_id}_{$zone}_{$ward->ward_no}";

            if (!DB::getSchemaBuilder()->hasTable($polygonTable)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Polygon table not found'
                ], 404);
            }

            $polygons = DB::table($polygonTable)->get();

            if ($polygons->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No polygon data found'
                ], 404);
            }

            $features = $polygons->map(function ($polygon) use ($pointTable, $buildingTable) {

                $coordinates = json_decode($polygon->coordinates, true);

                // ✅ Ensure float values
                $processed = array_map(function ($ring) {
                    return array_map(function ($point) {
                        return array_map('floatval', $point);
                    }, $ring);
                }, $coordinates);

                // ✅ Point check
                $pointExists = DB::getSchemaBuilder()->hasTable($pointTable)
                    ? DB::table($pointTable)->where('point_gisid', $polygon->gisid)->exists()
                    : false;

                // ✅ Building remarks
                $building = DB::getSchemaBuilder()->hasTable($buildingTable)
                    ? DB::table($buildingTable)->where('gisid', $polygon->gisid)->first()
                    : null;

                return [
                    'type' => 'Feature',
                    'properties' => [
                        'OBJECTID' => $polygon->id,
                        'GIS_ID' => $polygon->gisid,
                        'POINT_GISID' => $pointExists ? $polygon->gisid : null,
                        'BUILDING_REMARKS' => $building->remarks ?? null,
                        'created_at' => $polygon->created_at
                    ],
                    'geometry' => [
                        'type' => 'Polygon',
                        'coordinates' => $processed
                    ]
                ];
            });

            $geojson = [
                'type' => 'FeatureCollection',
                'name' => "ward_{$ward->ward_no}_polygons",
                'crs' => [
                    'type' => 'name',
                    'properties' => [
                        'name' => 'urn:ogc:def:crs:EPSG::3857'
                    ]
                ],
                'features' => $features->values()->all()
            ];

            $fileName = "polygon_ward_{$ward->ward_no}_{$zone}_" . date('Y-m-d') . ".geojson";

            return response()->json($geojson, 200, [
                'Content-Type' => 'application/geo+json',
                'Content-Disposition' => "attachment; filename={$fileName}"
            ]);
        } catch (\Exception $e) {
            Log::error("Polygon download error: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function cleanupFiles($files)
    {
        foreach ($files as $file) {
            try {
                if (File::exists(public_path($file))) {
                    File::delete(public_path($file));
                }
            } catch (\Exception $e) {
                Log::error("Failed to delete file {$file}: " . $e->getMessage());
            }
        }
    }
}

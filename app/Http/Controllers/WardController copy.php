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
use Illuminate\Support\Facades\Storage;
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
            'boundary' => 'nullable|file|mimes:geojson,json',
            'polygon' => 'nullable|file|mimes:geojson,json',
            'line' => 'nullable|file|mimes:geojson,json',
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

            // Handle drone image
            if ($request->hasFile('drone_image')) {
                $data['drone_image'] = $request->file('drone_image')
                    ->store('wards/drone_images', 'public');
                $uploadedFiles[] = $data['drone_image'];
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
            'boundary' => 'nullable|file|mimes:geojson,json',
            'polygon' => 'nullable|file',
            'status' => 'required|in:active,inactive',
            'line' => 'nullable|file|mimes:geojson,json'
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
            $polygonProcessResult = null;
            $lineProcessResult = null;
            $newFiles = [];

            if ($request->hasFile('drone_image')) {
                if ($ward->drone_image) {
                    Storage::disk('public')->delete($ward->drone_image);
                }
                $data['drone_image'] = $request->file('drone_image')
                    ->store('wards/drone_images', 'public');
                $newFiles[] = $data['drone_image'];
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

            if ($ward->drone_image) {
                Storage::disk('public')->delete($ward->drone_image);
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

    private function cleanupFiles($files)
    {
        foreach ($files as $file) {
            try {
                Storage::disk('public')->delete($file);
            } catch (\Exception $e) {
                Log::error("Failed to delete file {$file}: " . $e->getMessage());
            }
        }
    }
}

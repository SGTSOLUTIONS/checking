<?php

namespace App\Http\Controllers;

use App\Imports\MisImport;
use App\Imports\UgdImport;
use App\Imports\WatertaxImport;
use App\Models\Corporation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Schema\Blueprint;
use Maatwebsite\Excel\Facades\Excel;

class CorporationController extends Controller
{
    /** ✅ Load main view */
    public function corporationData()
    {
        return view('admin.corporationdata');
    }

    /** ✅ Store new corporation */
    public function corporationStore(Request $request)
    {
        try {
            // ✅ Validate first
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:50|unique:corporations,code',
                'district' => 'required|string|max:100',
                'state' => 'required|string|max:100',
                'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'boundary' => 'nullable|file|mimes:geojson,json|max:5120',
                'mis' => 'nullable|file|mimes:xls,xlsx,csv',
                'watertax' => 'nullable|file|mimes:xls,xlsx,csv',
                'ugd' => 'nullable|file|mimes:xls,xlsx,csv'
            ]);

            // ✅ Upload logo
            if ($request->hasFile('logo')) {
                $validatedData['logo'] = $request->file('logo')->store('corporation_logos', 'public');
            }

            // ✅ Extract GeoJSON
            if ($request->hasFile('boundary')) {
                $geojsonData = json_decode(file_get_contents($request->file('boundary')->getRealPath()), true);
                if (isset($geojsonData['features'][0]['geometry']['coordinates'])) {
                    $validatedData['boundary'] = json_encode($geojsonData['features'][0]['geometry']['coordinates']);
                } else {
                    throw new \Exception('Invalid GeoJSON format.');
                }
            }

            // ✅ Create corporation
            $corporation = Corporation::create($validatedData);

            // ✅ Create dynamic tables
            $this->createDynamicTables($corporation->id);

            // ✅ Excel imports
            if ($request->hasFile('mis')) {
                Excel::import(new MisImport('mis_corporation_' . $corporation->id, $corporation->id), $request->file('mis'));
            }
            if ($request->hasFile('watertax')) {
                Excel::import(new WatertaxImport('watertax_corporation_' . $corporation->id, $corporation->id), $request->file('watertax'));
            }
            if ($request->hasFile('ugd')) {
                Excel::import(new UgdImport('ugd_corporation_' . $corporation->id, $corporation->id), $request->file('ugd'));
            }

            return response()->json([
                'success' => true,
                'message' => 'Corporation created successfully!',
                'data' => $corporation
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Corporation store error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /** ✅ Create MIS, WaterTax & UGD tables dynamically */
    private function createDynamicTables($corporationId)
    {
        $misTable = 'mis_corporation_' . $corporationId;
        $waterTable = 'watertax_corporation_' . $corporationId;
        $ugdTable = 'ugd_corporation_' . $corporationId;
        $assinedRoadsTable = 'assigned_roads_corporation_' . $corporationId;

         // ✅ Assigned Roads table
        if (!Schema::hasTable($assinedRoadsTable)) {
            Schema::create($assinedRoadsTable, function (Blueprint $table) {
                $table->id();
                $table->foreignId('corporation_id')->constrained('corporations')->onDelete('cascade');
                $table->foreignId('team_id')->constrained('teams')->onDelete('cascade');
                $table->string('road_name')->nullable();
                $table->timestamps();
            });
        }


        // ✅ MIS table
        if (!Schema::hasTable($misTable)) {
            Schema::create($misTable, function (Blueprint $table) {
                $table->id();
                $table->foreignId('corporation_id')->constrained('corporations')->onDelete('cascade');
                $table->string('ward_no')->nullable();
                $table->string('assessment')->nullable();
                $table->string('old_assessment')->nullable();
                $table->string('road_name')->nullable();
                $table->string('owner_name')->nullable();
                $table->string('old_door_no')->nullable();
                $table->string('new_door_no')->nullable();
                $table->string('phone_number')->nullable();
                $table->string('plot_area')->nullable();
                $table->string('half_year_tax')->nullable();
                $table->string('balance')->nullable();
                $table->enum('usage', ['Residential', 'Commercial', 'Industrial', 'Institutional', 'Vacant'])->nullable();
                $table->enum('type', ['Owner', 'Tenant', 'Mixed', 'Government', 'Others'])->nullable();
                $table->string('zone')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // ✅ WaterTax table
        if (!Schema::hasTable($waterTable)) {
            Schema::create($waterTable, function (Blueprint $table) {
                $table->id();
                $table->foreignId('corporation_id')->constrained('corporations')->onDelete('cascade');
                $table->string('ward_no')->nullable();
                $table->string('assessment')->nullable();
                $table->string('road_name')->nullable();
                $table->string('watertax_no')->nullable();
                $table->string('old_watertax_no')->nullable();
                $table->string('old_door_no')->nullable();
                $table->string('new_door_no')->nullable();
                $table->string('phone_number')->nullable();
                $table->string('slap_rate')->nullable();
                $table->string('balance')->nullable();
                $table->enum('usage', ['Residential', 'Commercial', 'Industrial', 'Institutional', 'Vacant'])->nullable();
                $table->enum('slab_description', ['Domestic'])->nullable();
                $table->enum('DBC_type', ['Owner', 'Tenant', 'Mixed', 'Government', 'Others'])->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // ✅ UGD table
        if (!Schema::hasTable($ugdTable)) {
            Schema::create($ugdTable, function (Blueprint $table) {
                $table->id();
                $table->foreignId('corporation_id')->constrained('corporations')->onDelete('cascade');
                $table->string('ward_no')->nullable();
                $table->string('assessment')->nullable();
                $table->string('road_name')->nullable();
                $table->string('ugd_no')->nullable();
                $table->string('old_ugd_no')->nullable();
                $table->string('old_door_no')->nullable();
                $table->string('new_door_no')->nullable();
                $table->string('owner_name')->nullable();
                $table->string('phone_number')->nullable();
                $table->string('slap_rate')->nullable();
                $table->string('balance')->nullable();
                $table->enum('usage', ['Residential', 'Commercial', 'Industrial', 'Institutional', 'Vacant'])->nullable();
                $table->enum('slab_description', ['Domestic', 'Non-Domestic'])->nullable();
                $table->enum('DBC_type', ['Owner', 'Tenant', 'Mixed', 'Government', 'Others'])->nullable();
                $table->year('tax_year')->nullable();
                $table->decimal('ugd_tax_amount', 10, 2)->nullable();
                $table->decimal('ugd_tax_due', 10, 2)->nullable();
                $table->decimal('ugd_tax_paid', 10, 2)->nullable();
                $table->date('ugd_tax_paid_date')->nullable();
                $table->string('payment_mode')->nullable();
                $table->string('receipt_number')->nullable();
                $table->date('due_date')->nullable();
                $table->enum('status', ['Active', 'Inactive'])->default('Active');
                $table->text('remarks')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }


    public function corporationList()
    {
        try {
            $corporations = Corporation::select('id', 'name', 'code', 'district', 'state', 'logo')->get();
            return response()->json($corporations);
        } catch (\Exception $e) {
            Log::error('Corporation list error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /** ✅ Edit corporation */
    public function edit($id)
    {
        try {
            $corporation = Corporation::findOrFail($id);
            return response()->json($corporation);
        } catch (\Exception $e) {
            Log::error('Corporation edit error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Corporation not found.'], 404);
        }
    }

    /** ✅ Update corporation */
    public function update(Request $request, $id)
    {
        try {
            $corporation = Corporation::findOrFail($id);

            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:50|unique:corporations,code,' . $id,
                'district' => 'required|string|max:100',
                'state' => 'required|string|max:100',
                'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'boundary' => 'nullable|file|mimes:geojson,json|max:5120',
                'mis' => 'nullable|file|mimes:xls,xlsx,csv',
                'watertax' => 'nullable|file|mimes:xls,xlsx,csv',
                'ugd' => 'nullable|file|mimes:xls,xlsx,csv'
            ]);

            if ($request->hasFile('logo')) {
                if ($corporation->logo && Storage::disk('public')->exists($corporation->logo)) {
                    Storage::disk('public')->delete($corporation->logo);
                }
                $validatedData['logo'] = $request->file('logo')->store('corporation_logos', 'public');
            }

            if ($request->hasFile('boundary')) {
                $geojsonData = json_decode(file_get_contents($request->file('boundary')->getRealPath()), true);
                if (isset($geojsonData['features'][0]['geometry']['coordinates'])) {
                    $validatedData['boundary'] = json_encode($geojsonData['features'][0]['geometry']['coordinates']);
                }
            }

            $corporation->update($validatedData);
            $this->createDynamicTables($corporation->id);

            $importStats = [];

            if ($request->hasFile('mis')) {
                $misImport = new MisImport('mis_corporation_' . $corporation->id, $corporation->id);
                Excel::import($misImport, $request->file('mis'));
                $importStats['mis'] = $misImport->getImportStats();
            }

            if ($request->hasFile('watertax')) {
                $watertaxImport = new WatertaxImport('watertax_corporation_' . $corporation->id, $corporation->id);
                Excel::import($watertaxImport, $request->file('watertax'));
                $importStats['watertax'] = $watertaxImport->getImportStats();
            }

            if ($request->hasFile('ugd')) {
                $ugdImport = new UgdImport('ugd_corporation_' . $corporation->id, $corporation->id);
                Excel::import($ugdImport, $request->file('ugd'));
                $importStats['ugd'] = $ugdImport->getImportStats();
            }

            return response()->json([
                'success' => true,
                'message' => 'Corporation updated successfully!',
                'data' => $corporation,
                'import_stats' => $importStats
            ]);
        } catch (\Exception $e) {
            Log::error('Corporation update error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error updating corporation.', 'error' => $e->getMessage()], 500);
        }
    }

    /** ✅ Delete corporation (SoftDelete) */
    public function destroy($id)
    {
        try {
            $corporation = Corporation::findOrFail($id);

            if ($corporation->logo && Storage::disk('public')->exists($corporation->logo)) {
                Storage::disk('public')->delete($corporation->logo);
            }

            $corporation->delete();
            return response()->json(['success' => true, 'message' => 'Corporation deleted successfully!']);
        } catch (\Exception $e) {
            Log::error('Corporation delete error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error deleting corporation.', 'error' => $e->getMessage()], 500);
        }
    }

    /** ✅ Force delete corporation */
    public function forceDelete($id)
    {
        try {
            $corporation = Corporation::withTrashed()->findOrFail($id);

            if ($corporation->logo && Storage::disk('public')->exists($corporation->logo)) {
                Storage::disk('public')->delete($corporation->logo);
            }

            foreach (['mis_corporation_' . $id, 'watertax_corporation_' . $id, 'ugd_corporation_' . $id] as $table) {
                if (Schema::hasTable($table)) {
                    Schema::drop($table);
                }
            }

            $corporation->forceDelete();
            return response()->json(['success' => true, 'message' => 'Corporation permanently deleted!']);
        } catch (\Exception $e) {
            Log::error('Corporation force delete error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error permanently deleting corporation.', 'error' => $e->getMessage()], 500);
        }
    }
}

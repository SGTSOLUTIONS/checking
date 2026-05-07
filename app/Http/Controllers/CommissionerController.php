<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Corporation;
use App\Models\Ward;
use Carbon\Carbon;

class CommissionerController extends Controller
{
    /**
     * Display the commissioner dashboard with all data.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function dashboard()
    {
        // Get authenticated corporation user
        $user = Auth::guard('corporation')->user();

        if (!$user) {
            if (request()->wantsJson()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            return redirect()->route('corporation.login');
        }

        // Get corporation details
        $corporation = Corporation::find($user->corporation_id);

        if (!$corporation) {
            if (request()->wantsJson()) {
                return response()->json(['error' => 'Corporation not found'], 404);
            }
            return back()->with('error', 'Corporation not found');
        }

        // Get all wards for this corporation
        $wards = Ward::where('corporation_id', $corporation->id)
            ->where('status', 'active')
            ->get();
        $ward_count = Ward::where('corporation_id', $corporation->id)
            ->where('status', 'active')
            ->count();
        $wards_per_zones = Ward::where('corporation_id', $corporation->id)
            ->where('status', 'active')
            ->select('zone', DB::raw('count(*) as total'))
            ->groupBy('zone')
            ->get();

        $mis = DB::table("mis_corporation_{$corporation->id}")->get();
        $mis_count = DB::table("mis_corporation_{$corporation->id}")->count();
        $collections = [];

        foreach ($wards_per_zones as $wards_per_zone) {

            $wardlists = Ward::where('zone', $wards_per_zone->zone)->get();

            foreach ($wardlists as $wardlist) {

                $pointdatatable = $this->getpointdatatable(
                    $wardlist->corporation_id,
                    $wardlist->ward_no,
                    $wardlist->zone
                );

                $polygondatatable = $this->getpolygondatatable(
                    $wardlist->corporation_id,
                    $wardlist->ward_no,
                    $wardlist->zone
                );

                $polygontable = $this->getpolygontable(
                    $wardlist->corporation_id,
                    $wardlist->ward_no,
                    $wardlist->zone
                );

                $roadtable = $this->getroadtable(
                    $wardlist->corporation_id,
                    $wardlist->ward_no,
                    $wardlist->zone
                );

                $misData = DB::table("mis_corporation_{$wardlist->corporation_id}")
                    ->where('ward_no', $wardlist->ward_no)
                    ->get();

                $misCount = DB::table("mis_corporation_{$wardlist->corporation_id}")
                    ->where('ward_no', $wardlist->ward_no)
                    ->count();

                $data = [
                    "zone"               => $wardlist->zone,
                    "ward_no"            => $wardlist->ward_no,
                    "pointdatatable"     => $pointdatatable,
                    "polygondatatable"   => $polygondatatable,
                    "polygontable"       => $polygontable,
                    "roadtable"          => $roadtable,
                    "mis"                => $misData,
                    "misCount"           => $misCount,
                ];

                $collections[] = $data;
            }
        }

        return response()->json($collections);
        // Return view for web request
        return view('corporation.dashboard', compact('dashboardData'));
    }

    private function getpointdatatable($corporationId, $wardNo, $zone)
    {
        $tableName = "pointdata_{$corporationId}_{$zone}_{$wardNo}";

        if (Schema::hasTable($tableName)) {
            return $tableName;
        }

        return $tableName;
    }

    private function getpolygondatatable($corporationId, $wardNo, $zone)
    {
        $tableName = "polygondatatable_{$corporationId}_{$zone}_{$wardNo}";

        if (Schema::hasTable($tableName)) {
            return $tableName;
        }

        return $tableName;
    }

    private function getpolygontable($corporationId, $wardNo, $zone)
    {
        $tableName = "polygon_{$corporationId}_{$zone}_{$wardNo}";

        if (Schema::hasTable($tableName)) {
            return $tableName;
        }

        return $tableName;
    }
    private function getroadtable($corporationId, $wardNo, $zone)
    {
        $tableName = "line_{$corporationId}_{$zone}_{$wardNo}";

        if (Schema::hasTable($tableName)) {
            return $tableName;
        }

        return $tableName;
    }
}

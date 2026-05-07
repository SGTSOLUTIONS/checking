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
        $mis = DB::table("mis_corporation_{$corporation->id}")->get();
        $mis_count = DB::table("mis_corporation_{$corporation->id}")->count();
        return response()->json($mis_count);
        // Return view for web request
        return view('corporation.dashboard', compact('dashboardData'));
    }

}

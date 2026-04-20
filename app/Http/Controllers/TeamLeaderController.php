<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TeamLeaderController extends Controller
{


    public function dashboard()
    {
        $userId = Auth::user()->id;

        $teams = Team::with(['members', 'ward', 'corporation'])
            ->where('team_leader_id', $userId)
            ->get();

        $roadnames = [];

        foreach ($teams as $team) {

            $tableName = 'assigned_roads_corporation_' . $team->corporation->id;
            // ✅ Check table exists (VERY IMPORTANT)
            if (DB::getSchemaBuilder()->hasTable($tableName)) {
                $assignedRoads = DB::table($tableName)
                ->where("team_id", $team->id)
                ->pluck('road_name') // only road names
                ->toArray();
            } else {
                $assignedRoads = [];
            }

            $roadnames[$team->id] = $assignedRoads;
        }
        // return response()->json($roadnames);
        return view('teamleader.dashboard', compact('teams', 'roadnames'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Enums\ActiveStatusEnum;
use App\Enums\RoleEnum;
use Illuminate\Support\Facades\DB;
use App\Exports\MissingBillsExport;
use Maatwebsite\Excel\Facades\Excel;

class TeamController extends Controller
{
    /**
     * Show team management view
     */
    public function showTeams()
    {
        return view('admin.teams');
    }

    /**
     * Get all teams with related data
     */
    public function index()
    {
        try {
            $teams = Team::with(['teamLeader', 'ward.corporation', 'members'])->get();

            return response()->json([
                'success' => true,
                'teams' => $teams
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to load teams: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load teams'
            ], 500);
        }
    }

    /**
     * Store a new team
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'team_leader_id' => 'required|exists:users,id',
            'leader_name' => 'required|string|max:255',
            'ward_id' => 'required|exists:wards,id',
            'name' => 'required|string|max:255|unique:teams,name',
            'contact_number' => 'required|string|max:20',
            'status' => 'required|in:' . implode(',', array_column(ActiveStatusEnum::cases(), 'value'))
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {

            // ❌ Team Leader cannot lead two teams in the same ward
            $leaderAlreadyAssigned = Team::where('ward_id', $request->ward_id)
                ->where('team_leader_id', $request->team_leader_id)
                ->first();

            if ($leaderAlreadyAssigned) {
                return response()->json([
                    'success' => false,
                    'message' => 'This team leader is already assigned to another team in this ward.'
                ], 422);
            }

            // ❌ Team name must be unique inside same ward
            $nameExistsInWard = Team::where('ward_id', $request->ward_id)
                ->where('name', $request->name)
                ->first();

            if ($nameExistsInWard) {
                return response()->json([
                    'success' => false,
                    'message' => 'This team name already exists in the selected ward.'
                ], 422);
            }

            // ✅ Create team
            $team = Team::create([
                'ward_id' => $request->ward_id,
                'name' => $request->name,
                'leader_name' => $request->leader_name,
                'team_leader_id' => $request->team_leader_id,
                'contact_number' => $request->contact_number,
                'status' => $request->status
            ]);

            // ✔ Update role to team_leader if not already
            $user = User::find($request->team_leader_id);
            if ($user && $user->role !== 'team_leader') {
                $user->update(['role' => 'team_leader']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Team created successfully!',
                'team' => $team
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to create team: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create team: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single team details for editing
     */
    public function edit($id)
    {
        try {
            $team = Team::with(['teamLeader', 'ward.corporation', 'members'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'team' => $team
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to fetch team details: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch team details'
            ], 500);
        }
    }

    /**
     * Update team details
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:teams,name,' . $id,
            'contact_number' => 'required|string|max:20',
            'status' => 'required|in:' . implode(',', array_column(ActiveStatusEnum::cases(), 'value'))
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $team = Team::findOrFail($id);

            $team->update([
                'name' => $request->name,
                'contact_number' => $request->contact_number,
                'status' => $request->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Team updated successfully!',
                'team' => $team
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to update team: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update team: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add surveyor to team - WITH ONE SURVEYOR PER TEAM VALIDATION
     */
    public function addMember(Request $request, $teamId)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:surveyor,assistant'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $team = Team::findOrFail($teamId);
            $user = User::findOrFail($request->user_id);

            // ✅ Check if user is already a member of ANY team
            $existingTeamMember = DB::table('team_members')
                ->where('user_id', $request->user_id)
                ->first();

            if ($existingTeamMember) {
                return response()->json([
                    'success' => false,
                    'message' => 'This surveyor is already assigned to another team. Each surveyor can only work in one team.'
                ], 422);
            }

            // Check if user is already a member of THIS team
            if ($team->members()->where('user_id', $request->user_id)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is already a member of this team.'
                ], 422);
            }

            // Add member to team
            $team->members()->attach($request->user_id, [
                'role' => $request->role,
                'status' => 'active'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Member added to team successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to add team member: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to add team member: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove member from team
     */
    public function removeMember($teamId, $userId)
    {
        try {
            $team = Team::findOrFail($teamId);

            $team->members()->detach($userId);

            return response()->json([
                'success' => true,
                'message' => 'Member removed from team successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to remove team member: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove team member: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available surveyors for team assignment - EXCLUDE ALREADY ASSIGNED SURVEYORS
     */
    public function getAvailableSurveyors($teamId = null)
    {
        try {
            // Get surveyors who are not team leaders and not already in ANY team
            $query = User::where('role', 'surveyor')
                ->where('status', 'active')
                ->whereNotIn('id', function ($query) {
                    $query->select('user_id')
                        ->from('team_members');
                });

            $surveyors = $query->get(['id', 'name', 'email', 'phone']);

            return response()->json([
                'success' => true,
                'surveyors' => $surveyors
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to load available surveyors: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load available surveyors'
            ], 500);
        }
    }

    /**
     * Check if team can be deleted (prevent deletion if has active surveyors)
     */
    public function canDeleteTeam($teamId)
    {
        try {
            $team = Team::with(['members'])->findOrFail($teamId);

            // Team cannot be deleted if it has active members
            if ($team->members()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'can_delete' => false,
                    'message' => 'Cannot delete team that has active surveyors. Please remove all members first.'
                ]);
            }

            return response()->json([
                'success' => true,
                'can_delete' => true,
                'message' => 'Team can be deleted'
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to check team deletion: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to check team deletion'
            ], 500);
        }
    }

    /**
     * Delete a team and revert user role if needed - WITH VALIDATION
     */
    public function destroy($id)
    {
        try {
            $team = Team::with(['members'])->findOrFail($id);

            // Prevent deletion if team has active members
            if ($team->members()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete team that has active surveyors. Please remove all members first.'
                ], 422);
            }

            $user = $team->teamLeader;

            if ($user && $user->role === 'team_leader') {
                // Check if user leads other teams
                $otherTeams = Team::where('team_leader_id', $user->id)
                    ->where('id', '!=', $team->id)
                    ->count();

                if ($otherTeams === 0) {
                    $user->update(['role' => 'surveyor']);
                }
            }

            $team->delete();

            return response()->json([
                'success' => true,
                'message' => 'Team deleted successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to delete team: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete team: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all teams led by a specific user
     */
    public function getTeamsByLeader($leaderId)
    {
        try {
            $teams = Team::with(['ward.corporation', 'members'])
                ->where('team_leader_id', $leaderId)
                ->get();

            return response()->json([
                'success' => true,
                'teams' => $teams
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to load teams by leader: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load teams by leader'
            ], 500);
        }
    }
    // load roads to team
    public function loadRoads(Request $request, $teamId)
    {
        $teamid = $teamId;
        $teams = Team::with(['ward'])->findOrFail($teamid);
        $ward = $teams->ward;
        $misTable = 'mis_corporation_' . $teamid;
        try {
            $misdata = DB::table($misTable)
                ->where('ward_no', $ward->ward_no)
                ->distinct()
                ->pluck('road_name');

            return response()->json([
                'success' => true,
                'road_name' => $misdata,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to load teams by leader: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load teams by leader'
            ], 500);
        }
    }

    // assigned roads to team
    public function assignedRoads(Request $request)
    {
        $teamid = $request->team_id;

        $teams = Team::with(['ward'])->findOrFail($teamid);
        $ward = $teams->ward;

        $assinedRoadTable = 'assigned_roads_corporation_' . $teamid;

        try {

            // ✅ Check if already exists
            $alreadyExists = DB::table($assinedRoadTable)
                ->where('corporation_id', $ward->corporation_id)
                ->where('road_name', $request->road_name)
                ->exists();

            if ($alreadyExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'This road is already assigned for this corporation!'
                ]);
            }

            // ✅ Insert if not exists
            $data = DB::table($assinedRoadTable)->insert([
                'corporation_id' => $ward->corporation_id,
                'road_name' => $request->road_name,
                'team_id' => $teamid,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Road assigned to team successfully!',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            Log::error("Assign road error: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to assign road'
            ], 500);
        }
    }

    //downloadMissingBills
    public function downloadMissingBills(Request $request)
    {
        $teamId = $request->team_id;
        $team = Team::with(['ward', 'corporation'])->findOrFail($teamId);

        $roadName = $request->road_name;

        $polygondata = 'polygondata_' . $team->corporation->id . '_' . $team->ward->zone . '_' . $team->ward->ward_no;
        $pointdata   = 'pointdata_' . $team->corporation->id . '_' . $team->ward->zone . '_' . $team->ward->ward_no;
        $misdata     = 'mis_corporation_' . $team->corporation->id;

        try {

            $allData = collect();

            if ($roadName == 'all') {
                $assigned_roads = 'assigned_roads_corporation_' . $team->ward->corporation_id;
                // ✅ FIXED table name (change this table name correctly)
                $assignedRoads = DB::table($assigned_roads) // <-- FIX THIS TABLE NAME
                    ->where('team_id', $team->id)
                    ->distinct()
                    ->pluck('road_name');

                foreach ($assignedRoads as $road) {

                    $data = DB::table($misdata . ' as m')
                        ->leftJoin($pointdata . ' as p', function ($join) {
                            $join->on(
                                DB::raw('TRIM(m.assessment)'),
                                '=',
                                DB::raw('TRIM(p.assessment)')
                            );
                        })
                        ->whereNull('p.assessment') // ✅ NOT present in pointdata
                        ->where('m.road_name', $roadName)
                        ->where('m.ward_no', $team->ward->ward_no)
                        ->select('m.*')
                        ->get();
                    // ✅ Merge all roads data
                    $allData = $allData->merge($data);
                }
            } else {

                $allData = DB::table($misdata . ' as m')
                    ->leftJoin($pointdata . ' as p', function ($join) {
                        $join->on(
                            DB::raw('TRIM(m.assessment)'),
                            '=',
                            DB::raw('TRIM(p.assessment)')
                        );
                    })
                    ->whereNull('p.assessment') // ✅ NOT present in pointdata
                    ->where('m.road_name', $roadName)
                    ->where('m.ward_no', $team->ward->ward_no)
                    ->select('m.*')
                    ->get();
            }

            // ✅ DOWNLOAD EXCEL
            return Excel::download(
                new MissingBillsExport($allData),
                'missing_bills.xlsx'
            );
        } catch (\Exception $e) {

            Log::error("Failed to download missing bills: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to download missing bills'
            ], 500);
        }
    }
}

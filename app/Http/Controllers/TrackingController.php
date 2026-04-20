<?php

namespace App\Http\Controllers;

use App\Models\Tracking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TrackingController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Not logged in'], 401);
        }

        if ($user->role !== 'surveyor') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        // Store in UTC (default)
        Tracking::create([
            'user_id' => $user->id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'tracked_at' => now() // This will be UTC
        ]);

        return response()->json(['message' => 'Location stored', 'status' => 'success']);
    }

    // Get tracking data for a specific user with date range
    public function getUserTracking(Request $request, $userId = null)
    {
        try {
            $user = Auth::user();

            // If no userId provided, get current user's data
            $targetUserId = $userId ?? $user->id;

            // Check permission
            if ($user->role !== 'admin' && $user->id != $targetUserId) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $query = Tracking::with('user')
                ->where('user_id', $targetUserId)
                ->orderBy('tracked_at', 'asc');

            // Apply date filters
            if ($request->has('start_date') && $request->start_date) {
                $startDate = Carbon::parse($request->start_date)->startOfDay();
                $query->where('tracked_at', '>=', $startDate);
            }

            if ($request->has('end_date') && $request->end_date) {
                $endDate = Carbon::parse($request->end_date)->endOfDay();
                $query->where('tracked_at', '<=', $endDate);
            }

            if ($request->has('start_time') && $request->start_time) {
                $query->whereTime('tracked_at', '>=', $request->start_time);
            }

            if ($request->has('end_time') && $request->end_time) {
                $query->whereTime('tracked_at', '<=', $request->end_time);
            }

            $trackingData = $query->get();

            // Convert times to IST (Indian Standard Time)
            $trackingData = $trackingData->map(function($item) {
                // Convert UTC to IST (UTC+5:30)
                $item->tracked_at_local = Carbon::parse($item->tracked_at)
                    ->setTimezone('Asia/Kolkata')
                    ->format('Y-m-d H:i:s');

                $item->tracked_at_formatted = Carbon::parse($item->tracked_at)
                    ->setTimezone('Asia/Kolkata')
                    ->format('d M Y, h:i:s A');

                return $item;
            });

            return response()->json([
                'success' => true,
                'data' => $trackingData,
                'user' => $trackingData->first()->user ?? null,
                'timezone' => 'Asia/Kolkata'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Get tracking summary for dashboard
    public function getTrackingSummary(Request $request)
    {
        try {
            $user = Auth::user();

            $query = Tracking::where('user_id', $user->id);

            // Convert to IST for calculations
            $today = Carbon::now('Asia/Kolkata')->startOfDay();
            $weekStart = Carbon::now('Asia/Kolkata')->startOfWeek();
            $monthStart = Carbon::now('Asia/Kolkata')->startOfMonth();

            // Today's tracking count
            $todayCount = (clone $query)->where('tracked_at', '>=', $today)->count();

            // This week's tracking count
            $weekCount = (clone $query)->where('tracked_at', '>=', $weekStart)->count();

            // This month's tracking count
            $monthCount = (clone $query)->where('tracked_at', '>=', $monthStart)->count();

            // Last location with IST time
            $lastLocation = (clone $query)->latest('tracked_at')->first();
            if ($lastLocation) {
                $lastLocation->tracked_at_local = Carbon::parse($lastLocation->tracked_at)
                    ->setTimezone('Asia/Kolkata')
                    ->format('Y-m-d H:i:s');
            }

            // Total points
            $allPoints = (clone $query)->orderBy('tracked_at', 'asc')->get();
            $totalDistance = $this->calculateTotalDistance($allPoints);

            return response()->json([
                'success' => true,
                'summary' => [
                    'today_count' => $todayCount,
                    'week_count' => $weekCount,
                    'month_count' => $monthCount,
                    'total_distance' => round($totalDistance, 2),
                    'last_location' => $lastLocation,
                    'total_points' => $allPoints->count()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // MAP VIEW METHOD
    public function mapView(Request $request)
    {
        $userId = $request->get('user_id');
        $userName = $request->get('name', 'Surveyor');
        $userRole = $request->get('role', '');

        // If user_id is provided, get the user details
        if ($userId) {
            $user = User::find($userId);
            if ($user) {
                $userName = $user->name;
                $userRole = $user->role;
            }
        }

        return view('admin.tracking.map-view', compact('userId', 'userName', 'userRole'));
    }

    // Calculate total distance between points
    private function calculateTotalDistance($points)
    {
        $totalDistance = 0;

        for ($i = 0; $i < count($points) - 1; $i++) {
            $lat1 = floatval($points[$i]->latitude);
            $lon1 = floatval($points[$i]->longitude);
            $lat2 = floatval($points[$i + 1]->latitude);
            $lon2 = floatval($points[$i + 1]->longitude);

            $theta = $lon1 - $lon2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            $km = $miles * 1.609344;

            $totalDistance += $km;
        }

        return $totalDistance;
    }
}

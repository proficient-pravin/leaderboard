<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    /**
     * Display the leaderboard based on selected filters and search parameters.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Determine the rank column based on the selected filter period
        $rankColumn = match ($request->filter) {
            'day' => 'daily_rank',
            'month' => 'monthly_rank',
            'year' => 'yearly_rank',
            default => 'daily_rank' // Default to daily rank if no filter is specified
        };

        // Get the base query for users with activities filtered by the selected period
        $usersQuery = User::with(['activities' => function ($query) use ($request) {
            // Filter activities based on the selected period
            if ($request->filter == 'day' || !$request->has('filter')) {
                $query->whereDate('activity_time', Carbon::today());
            } elseif ($request->filter == 'month') {
                $query->whereMonth('activity_time', Carbon::now()->month)
                    ->whereYear('activity_time', Carbon::now()->year);
            } elseif ($request->filter == 'year') {
                $query->whereYear('activity_time', Carbon::now()->year);
            }
        }]);

        // Retrieve users, calculate total_points for the filtered period, and order by the selected rank
        $users = $usersQuery
            ->orderBy($rankColumn)
            ->whereNotNull($rankColumn)
            ->get()
            ->map(function ($user) {
                $user->total_points = $user->activities->sum('points'); // Calculate total points
                return $user;
            });

        // If searching, move the matched user to the top of the leaderboard
        if ($request->has('user_id') && !empty($request->user_id)) {
            $searchedUser = $users->firstWhere('id', $request->user_id);
            if ($searchedUser) {
                $users = $users->reject(fn($item) => $item->id == $searchedUser->id);
                $users->prepend($searchedUser);
            }
        }

        return view('leaderboard.index', compact('users'));
    }

    /**
     * Calculate and store ranks for all periods (daily, monthly, yearly).
     *
     * @return \Illuminate\Http\JsonResponse | void
     */
    public function calculateAndStorePeriodicRanks()
    {
        // Calculate and store ranks for each period
        $this->calculateRankForPeriod('daily_rank', Carbon::today(), 'day');
        $this->calculateRankForPeriod('monthly_rank', Carbon::now()->startOfMonth(), 'month');
        $this->calculateRankForPeriod('yearly_rank', Carbon::now()->startOfYear(), 'year');

        if (request()->ajax()) {
            return response()->json([
                'message' => 'Rank calculation successful.',
            ]);
        }
    }

    /**
     * Calculate ranks for a specified period and store them in the user model.
     * Users with the same points receive the same rank.
     * Next different point value receives the next sequential rank.
     *
     * @param string $rankColumn
     * @param \Carbon\Carbon $startDate
     * @param string $period
     * @return void
     */
    public function calculateRankForPeriod($rankColumn, $startDate, $period)
    {
        // Retrieve users with activities filtered by the specified period
        $users = User::with(['activities' => function ($query) use ($startDate, $period) {
            if ($period === 'day') {
                $query->whereDate('activity_time', $startDate);
            } elseif ($period === 'month') {
                $query->whereMonth('activity_time', $startDate->month)
                    ->whereYear('activity_time', $startDate->year);
            } elseif ($period === 'year') {
                $query->whereYear('activity_time', $startDate->year);
            }
        }])
            ->get()
            ->map(function ($user) {
                $user->total_points = $user->activities->sum('points');
                return $user;
            })
            ->sortByDesc('total_points')
            ->values();

        $currentRank = 1;
        $prevPoints = null;

        foreach ($users as $user) {
            if ($prevPoints !== null && $user->total_points !== $prevPoints) {
                // Points are different from previous user, increment rank
                $currentRank++;
            }

            // Assign current rank to user
            $user->{$rankColumn} = $currentRank;
            $prevPoints = $user->total_points;

            unset($user->total_points);
            $user->save();
        }
    }
}

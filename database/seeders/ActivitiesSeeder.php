<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ActivitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::all()->each(function ($user) {
            for ($i = 0; $i < rand(1, 10); $i++) {
                Activity::create([
                    'user_id' => $user->id,
                    'points' => 20,
                    'activity_time' => Carbon::now()->subDays(rand(0, 30)),
                ]);
            }
        });

        // Just uncomment the following line if you do not want to calculate the rank after seeding
        // app(LeaderboardController::class)->calculateAndStorePeriodicRanks();
    }
}

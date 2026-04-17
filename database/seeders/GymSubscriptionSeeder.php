<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GymSubscription;
use App\Models\Gym;
use App\Models\User;
use App\Models\GymFeePlan;
use Illuminate\Support\Str;

class GymSubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        $gyms = Gym::all();
        $users = User::all();

        if ($gyms->isEmpty() || $users->isEmpty()) {
            return;
        }

        foreach ($users as $user) {
            $gym = $gyms->random();
            $plan = $gym->feePlans->random() ?? null;

            if ($plan) {
                GymSubscription::create([
                    'id' => Str::uuid(),
                    'gym_id' => $gym->id,
                    'user_id' => $user->id,
                    'gym_fee_plan_id' => $plan->id,
                    'start_date' => now()->subDays(rand(0, 30)),
                    'end_date' => now()->addDays(rand(1, 60)),
                    'amount_paid' => $plan->price,
                    'payment_status' => 'paid',
                    'payment_method' => 'UPI',
                    'status' => 'active',
                ]);
            }
        }
    }
}

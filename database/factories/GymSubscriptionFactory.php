<?php

namespace Database\Factories;

use App\Models\GymSubscription;
use App\Models\Gym;
use App\Models\User;
use App\Models\GymFeePlan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class GymSubscriptionFactory extends Factory
{
    protected $model = GymSubscription::class;

    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-6 months', 'now');
        $endDate = (clone $startDate)->modify('+1 month');

        return [
            'id' => Str::uuid(),
            'gym_id' => Gym::factory(),
            'user_id' => User::factory(),
            'gym_fee_plan_id' => GymFeePlan::factory(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'amount_paid' => $this->faker->randomFloat(2, 500, 5000),
            'payment_status' => $this->faker->randomElement(['paid', 'pending', 'failed']),
            'payment_method' => $this->faker->randomElement(['UPI', 'Card', 'Cash']),
            'status' => $this->faker->randomElement(['active', 'expired', 'cancelled']),
            'notes' => $this->faker->sentence(),
        ];
    }
}

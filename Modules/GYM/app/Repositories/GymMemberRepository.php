<?php

namespace Modules\GYM\app\Repositories;

use Modules\GYM\app\Interfaces\GymMemberRepositoryInterface;
use App\Models\GymSubscription;
use App\Models\User;
use App\Models\GymFeePlan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class GymMemberRepository implements GymMemberRepositoryInterface
{
    public function getMembersByGym(string $gymId, int $perPage = 15)
    {
        return GymSubscription::with(['user', 'plan'])
            ->where('gym_id', $gymId)
            ->latest()
            ->paginate($perPage);
    }

    public function addMember(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Find user by phone, or create a new one
            $user = User::where('phone', $data['phone'])->first();
            
            if (!$user) {
                // If email is provided, check if it exists too
                if (!empty($data['email'])) {
                    $user = User::where('email', $data['email'])->first();
                }

                if (!$user) {
                    $user = User::create([
                        'name' => $data['name'],
                        'email' => $data['email'] ?? null,
                        'phone' => $data['phone'],
                        'password' => Hash::make(Str::random(12)),
                        'user_type' => 2, // Assuming 2 is member
                        'status' => true,
                    ]);
                }
            }
            
            // Calculate end date based on plan duration
            $plan = GymFeePlan::findOrFail($data['gym_fee_plan_id']);
            $startDate = Carbon::parse($data['start_date']);
            $endDate = $startDate->copy()->addMonths($plan->duration_months);

            // Create the subscription
            $subscription = GymSubscription::create([
                'gym_id' => $data['gym_id'],
                'user_id' => $user->id,
                'gym_fee_plan_id' => $plan->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'amount_paid' => $data['amount_paid'],
                'payment_status' => 'paid',
                'payment_method' => $data['payment_method'] ?? 'cash',
                'status' => 'active',
                'notes' => $data['notes'] ?? null,
            ]);

            return $subscription->load(['user', 'plan']);
        });
    }

    public function getMemberDetails(string $subscriptionId)
    {
        return GymSubscription::with(['user', 'plan', 'gym'])
            ->findOrFail($subscriptionId);
    }
}

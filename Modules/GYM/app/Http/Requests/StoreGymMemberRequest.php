<?php

namespace Modules\GYM\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGymMemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'gym_id' => 'required|uuid|exists:gyms,id',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'gym_fee_plan_id' => 'required|uuid|exists:gym_fee_plans,id',
            'start_date' => 'required|date',
            'amount_paid' => 'required|numeric|min:0',
            'payment_method' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            
            // User Profile Fields
            'alternative_contact' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other,prefer_not_to_say',
            'age' => 'nullable|integer|min:1|max:120',
            'current_weight' => 'nullable|numeric|min:1',
            'height' => 'nullable|numeric|min:1',
            'goal_type' => 'nullable|in:weight_gain,weight_loss,maintenance,muscle_building',
            'activity_level' => 'nullable|in:sedentary,lightly_active,moderately_active,very_active,extra_active',
            'diet_type' => 'nullable|in:veg,non_veg,eggitarian,vegan,keto,paleo',
            'medical_conditions' => 'nullable|string',
            'allergies' => 'nullable|string',
            'physical_limitations' => 'nullable|string',
            'is_public' => 'nullable|boolean',
        ];
    }
}

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
        ];
    }
}

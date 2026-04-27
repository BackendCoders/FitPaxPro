<?php

namespace Modules\GYM\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGymRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $gymId = $this->route('id');
        return [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:gyms,email,' . $gymId,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'member_count_limit' => 'nullable|integer',
            'platform_plan_id' => 'nullable|exists:platform_subscription_plans,id',
            'image' => 'nullable|image|max:5120',
            'gallery' => 'nullable|array',
            'gallery.*' => 'image|max:5120',
            'youtube_links' => 'nullable|array',
            'youtube_links.*' => 'nullable|url',
            'custom_fields' => 'nullable|array',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}

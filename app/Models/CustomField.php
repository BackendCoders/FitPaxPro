<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomField extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'label',
        'model_type',
        'type',
        'validation_rules',
        'options',
        'placeholder',
        'help_text',
        'default_value',
        'order',
        'is_active',
        'is_required',
    ];

    protected $casts = [
        'options' => 'array',
        'is_active' => 'boolean',
        'is_required' => 'boolean',
    ];

    public function values()
    {
        return $this->hasMany(CustomFieldValue::class);
    }
}

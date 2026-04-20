<?php

namespace App\Models\Concerns;

use App\Models\CustomField;
use App\Models\CustomFieldValue;

trait HasCustomFields
{
    /**
     * Get all custom field values for this model formatted for API.
     */
    public function getCustomFieldsDataAttribute()
    {
        $fields = CustomField::where('model_type', get_class($this))
            ->where('is_active', true)
            ->get();
            
        $data = [];
        foreach ($fields as $field) {
            $value = CustomFieldValue::where('custom_field_id', $field->id)
                ->where('model_id', $this->id)
                ->first();
                
            $data[$field->name] = $value ? $value->value : $field->default_value;
        }
        return $data;
    }
    
    // Correction: I used model_id but not model_type in custom_field_values.
    // However, I have model_type in custom_fields table.
    // So for a specific instance of "Gym", I want to get values where custom_field's model_type is "Gym" and values' model_id is this->id.
    
    public function getCustomFields()
    {
        return CustomField::where('model_type', get_class($this))
            ->where('is_active', true)
            ->orderBy('order')
            ->get();
    }
    
    public function getCustomFieldValue($fieldName)
    {
        $field = CustomField::where('model_type', get_class($this))
            ->where('name', $fieldName)
            ->first();
            
        if (!$field) return null;
        
        $value = CustomFieldValue::where('custom_field_id', $field->id)
            ->where('model_id', $this->id)
            ->first();
            
        return $value ? $value->value : $field->default_value;
    }
    
    public function saveCustomFields(array $values)
    {
        $fields = $this->getCustomFields();
        
        foreach ($fields as $field) {
            if (array_key_exists($field->name, $values)) {
                $value = $values[$field->name];
                CustomFieldValue::updateOrCreate(
                    [
                        'custom_field_id' => $field->id,
                        'model_id' => $this->id,
                    ],
                    [
                        'value' => is_array($value) ? json_encode($value) : $value
                    ]
                );
            }
        }
    }

    public static function getCustomFieldRules($modelType)
    {
        $fields = CustomField::where('model_type', $modelType)
            ->where('is_active', true)
            ->get();
            
        $rules = [];
        foreach ($fields as $field) {
            $fieldRules = $field->validation_rules ?? '';
            if ($field->is_required && !str_contains($fieldRules, 'required')) {
                $fieldRules = 'required|' . $fieldRules;
            }
            
            if ($fieldRules) {
                $rules["custom_fields.{$field->name}"] = trim($fieldRules, '|');
            }
        }
        
        return $rules;
    }
}

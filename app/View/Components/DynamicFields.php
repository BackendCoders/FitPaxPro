<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DynamicFields extends Component
{
    public $model_type;
    public $model;
    public $fields;

    /**
     * Create a new component instance.
     */
    public function __construct($modelType, $model = null)
    {
        $this->model_type = $modelType;
        $this->model = $model;
        $this->fields = \App\Models\CustomField::where('model_type', $modelType)
            ->where('is_active', true)
            ->orderBy('order')
            ->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.dynamic-fields');
    }
}

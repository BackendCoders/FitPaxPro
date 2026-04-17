<?php

namespace Modules\Admin\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CustomField;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CustomFieldController extends Controller
{
    public function index()
    {
        $customFields = CustomField::orderBy('model_type')->orderBy('order')->get();
        return view('admin::custom-fields.index', compact('customFields'));
    }

    public function create()
    {
        $fieldTypes = [
            'text' => 'Text',
            'textarea' => 'TextArea',
            'number' => 'Number',
            'select' => 'Select Dropdown',
            'checkbox' => 'Checkbox',
            'radio' => 'Radio Buttons',
            'date' => 'Date',
            'email' => 'Email',
        ];

        $models = [
            'App\Models\Gym' => 'Gym',
            'App\Models\MembershipPlanTemplate' => 'Membership Plan',
            'App\Models\User' => 'User/Member',
        ];

        return view('admin::custom-fields.create', compact('fieldTypes', 'models'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'model_type' => 'required|string',
            'type' => 'required|string',
            'validation_rules' => 'nullable|string',
            'options' => 'nullable|string',
            'placeholder' => 'nullable|string',
            'help_text' => 'nullable|string',
            'default_value' => 'nullable|string',
            'order' => 'required|integer',
        ]);

        try {
            $data = $validated;
            $data['name'] = Str::slug($validated['label'], '_');
            
            if ($request->filled('options')) {
                $data['options'] = array_map('trim', explode(',', $request->options));
            }
            
            $data['is_active'] = $request->has('is_active');
            $data['is_required'] = $request->has('is_required');

            CustomField::create($data);

            return redirect()->route('admin.custom-fields.index')->with('success', 'Tactical data node initialized successfully.');
        } catch (\Exception $e) {
            \Log::error('Custom Field Creation Error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Deployment Failed: ' . $e->getMessage());
        }
    }

    public function edit(CustomField $customField)
    {
        $fieldTypes = [
            'text' => 'Text',
            'textarea' => 'TextArea',
            'number' => 'Number',
            'select' => 'Select Dropdown',
            'checkbox' => 'Checkbox',
            'radio' => 'Radio Buttons',
            'date' => 'Date',
            'email' => 'Email',
        ];

        $models = [
            'App\Models\Gym' => 'Gym',
            'App\Models\MembershipPlanTemplate' => 'Membership Plan',
            'App\Models\User' => 'User/Member',
        ];

        return view('admin::custom-fields.edit', compact('customField', 'fieldTypes', 'models'));
    }

    public function update(Request $request, CustomField $customField)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'model_type' => 'required|string',
            'type' => 'required|string',
            'validation_rules' => 'nullable|string',
            'options' => 'nullable|string',
            'placeholder' => 'nullable|string',
            'help_text' => 'nullable|string',
            'default_value' => 'nullable|string',
            'order' => 'required|integer',
        ]);

        try {
            $data = $validated;
            if ($request->filled('options')) {
                $data['options'] = array_map('trim', explode(',', $request->options));
            } else {
                $data['options'] = null;
            }
            
            $data['name'] = Str::slug($validated['label'], '_');
            $data['is_active'] = $request->has('is_active');
            $data['is_required'] = $request->has('is_required');

            $customField->update($data);

            return redirect()->route('admin.custom-fields.index')->with('success', 'Data node calibration updated.');
        } catch (\Exception $e) {
            \Log::error('Custom Field Update Error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Protocol Update Failed: ' . $e->getMessage());
        }
    }

    public function destroy(CustomField $customField)
    {
        $customField->delete();
        return redirect()->route('admin.custom-fields.index')->with('success', 'Custom field deleted successfully.');
    }

    public function toggleStatus(CustomField $customField)
    {
        try {
            $customField->is_active = !$customField->is_active;
            $customField->save();

            return response()->json([
                'success' => true,
                'is_active' => (bool)$customField->is_active,
                'message' => 'Custom field ' . ($customField->is_active ? 'ENABLED' : 'DISABLED')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Protocol Error: ' . $e->getMessage()
            ], 500);
        }
    }
}

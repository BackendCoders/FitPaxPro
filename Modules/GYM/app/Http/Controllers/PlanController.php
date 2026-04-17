<?php

namespace Modules\GYM\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MembershipPlanTemplate;

class PlanController extends Controller
{
    /**
     * Display a listing of membership plan templates.
     */
    public function index()
    {
        $plans = MembershipPlanTemplate::latest()->get();
        return view('gym::plans.index', compact('plans'));
    }

    /**
     * Show the form for creating a new template.
     */
    public function create()
    {
        return view('gym::plans.create');
    }

    /**
     * Store a newly created template in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'offer_price' => 'nullable|numeric|min:0',
            'duration_months' => 'required|integer|min:1',
            'includes_diet_plan' => 'boolean',
            'includes_trainer' => 'boolean',
        ];

        $dynamicRules = MembershipPlanTemplate::getCustomFieldRules(MembershipPlanTemplate::class);
        $request->validate(array_merge($rules, $dynamicRules));

        $data = $request->except(['features_list']);
        $data['includes_diet_plan'] = $request->has('includes_diet_plan');
        $data['includes_trainer'] = $request->has('includes_trainer');

        if ($request->filled('features_list')) {
            $data['features'] = array_filter(explode("\n", str_replace("\r", "", $request->features_list)));
        }

        $plan = MembershipPlanTemplate::create($data);
        
        if ($request->has('custom_fields')) {
            $plan->saveCustomFields($request->custom_fields);
        }

        return redirect()->route('gym.plans.index')->with('success', 'Global membership plan created successfully!');
    }

    /**
     * Show the form for editing the specified template.
     */
    public function edit($id)
    {
        $plan = MembershipPlanTemplate::findOrFail($id);
        return view('gym::plans.edit', compact('plan'));
    }

    /**
     * Update the specified template in storage.
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'offer_price' => 'nullable|numeric|min:0',
            'duration_months' => 'required|integer|min:1',
        ];

        $dynamicRules = MembershipPlanTemplate::getCustomFieldRules(MembershipPlanTemplate::class);
        $request->validate(array_merge($rules, $dynamicRules));

        $plan = MembershipPlanTemplate::findOrFail($id);
        
        $data = $request->except(['features_list', 'custom_fields']);
        $data['includes_diet_plan'] = $request->has('includes_diet_plan');
        $data['includes_trainer'] = $request->has('includes_trainer');

        if ($request->filled('features_list')) {
             $data['features'] = array_filter(explode("\n", str_replace("\r", "", $request->features_list)));
        }

        $plan->update($data);

        if ($request->has('custom_fields')) {
            $plan->saveCustomFields($request->custom_fields);
        }

        return redirect()->route('gym.plans.index')->with('success', 'Global membership plan updated successfully!');
    }

    /**
     * Remove the specified template from storage.
     */
    public function destroy($id)
    {
        MembershipPlanTemplate::findOrFail($id)->delete();
        return redirect()->route('gym.plans.index')->with('success', 'Global membership plan deleted successfully!');
    }

    /**
     * Toggle the active status of a plan.
     */
    public function toggleStatus($id)
    {
        try {
            $plan = MembershipPlanTemplate::findOrFail($id);
            $plan->is_active = !$plan->is_active;
            $plan->save();

            return response()->json([
                'success' => true,
                'is_active' => (bool)$plan->is_active,
                'message' => 'Node authorization ' . ($plan->is_active ? 'ENABLED' : 'DISABLED')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Critical Protocol Failure: ' . $e->getMessage()
            ], 500);
        }
    }
}

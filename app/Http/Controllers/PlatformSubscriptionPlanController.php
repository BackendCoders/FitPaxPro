<?php

namespace App\Http\Controllers;

use App\Models\PlatformSubscriptionPlan;
use Illuminate\Http\Request;

class PlatformSubscriptionPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PlatformSubscriptionPlan::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $plans = $query->latest()->get();
        return view('admin.platform_plans.index', compact('plans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.platform_plans.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'monthly_price' => 'required|numeric|min:0',
            'yearly_price' => 'nullable|numeric|min:0',
            'max_gyms' => 'required|integer|min:1',
            'max_members' => 'nullable|integer|min:1',
        ]);

        $data = $request->all();
        $data['has_analytics'] = $request->has('has_analytics');
        $data['has_mobile_app'] = $request->has('has_mobile_app');

        PlatformSubscriptionPlan::create($data);

        return redirect()->route('admin.platform-plans.index')->with('success', 'Platform plan created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $plan = PlatformSubscriptionPlan::findOrFail($id);
        return view('admin.platform_plans.edit', compact('plan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'monthly_price' => 'required|numeric|min:0',
            'max_gyms' => 'required|integer|min:1',
        ]);

        $plan = PlatformSubscriptionPlan::findOrFail($id);
        
        $data = $request->all();
        $data['has_analytics'] = $request->has('has_analytics');
        $data['has_mobile_app'] = $request->has('has_mobile_app');

        $plan->update($data);

        return redirect()->route('admin.platform-plans.index')->with('success', 'Platform plan updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        PlatformSubscriptionPlan::findOrFail($id)->delete();
        return redirect()->route('admin.platform-plans.index')->with('success', 'Platform plan deleted successfully.');
    }
}

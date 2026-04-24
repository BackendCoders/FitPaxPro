<?php

namespace Modules\Admin\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('order', 'asc')->get();
        return view('admin::banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin::banners.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'badge_text' => 'nullable|string|max:50',
            'background_color_hex' => 'nullable|string|max:7',
            'target_link' => 'nullable|string|max:255',
            'order' => 'integer',
        ]);

        $data = $request->except('image');
        
        if ($request->hasFile('image')) {
            $data['image_url'] = $request->file('image')->store('banners', 'public');
        }

        Banner::create($data);

        return redirect()->route('admin.banners.index')->with('success', 'Banner deployed successfully.');
    }

    public function edit(Banner $banner)
    {
        return view('admin::banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'badge_text' => 'nullable|string|max:50',
            'background_color_hex' => 'nullable|string|max:7',
            'target_link' => 'nullable|string|max:255',
            'order' => 'integer',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            if ($banner->image_url) {
                Storage::disk('public')->delete($banner->image_url);
            }
            $data['image_url'] = $request->file('image')->store('banners', 'public');
        }

        $banner->update($data);

        return redirect()->route('admin.banners.index')->with('success', 'Banner intelligence updated.');
    }

    public function destroy(Banner $banner)
    {
        if ($banner->image_url) {
            Storage::disk('public')->delete($banner->image_url);
        }
        $banner->delete();

        return redirect()->route('admin.banners.index')->with('success', 'Banner decommissioned.');
    }

    public function toggleStatus(Banner $banner)
    {
        $banner->update(['is_active' => !$banner->is_active]);
        return response()->json(['success' => true, 'status' => $banner->is_active]);
    }
}

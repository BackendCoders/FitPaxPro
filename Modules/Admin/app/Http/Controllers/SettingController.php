<?php

namespace Modules\Admin\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Admin\app\Interfaces\SettingRepositoryInterface;
use App\Services\SettingService;

class SettingController extends Controller
{
    private $settingRepository;
    private $settingService;

    public function __construct(
        SettingRepositoryInterface $settingRepository,
        SettingService $settingService
    ) {
        $this->settingRepository = $settingRepository;
        $this->settingService = $settingService;
    }

    /**
     * Display the settings dashboard.
     */
    public function index()
    {
        $settings = $this->settingRepository->getSettings();
        return view('admin::settings.index', compact('settings'));
    }

    /**
     * Update global settings.
     */
    public function update(Request $request)
    {
        // Handle file uploads first
        if ($request->hasFile('logo')) {
            $this->settingRepository->uploadImage('logo', $request->file('logo'));
        }

        if ($request->hasFile('favicon')) {
            $this->settingRepository->uploadImage('favicon', $request->file('favicon'));
        }

        // Handle text settings
        $settingsToUpdate = $request->only([
            'site_title',
            'admin_email',
            'currency',
            'currency_symbol',
            'contact_number',
            'office_address'
        ]);

        $this->settingRepository->updateSettings($settingsToUpdate);

        // Clear cache so changes reflect instantly
        $this->settingService->clearCache();

        return redirect()->back()->with('success', 'Settings updated successfully!');
    }
}

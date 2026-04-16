<?php

namespace Modules\Admin\app\Repositories;

use App\Models\Setting;
use Modules\Admin\app\Interfaces\SettingRepositoryInterface;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SettingRepository implements SettingRepositoryInterface
{
    /**
     * Get all settings as a key-value pair.
     */
    public function getSettings()
    {
        return Setting::pluck('value', 'key')->toArray();
    }

    /**
     * Update multiple settings.
     */
    public function updateSettings(array $settings)
    {
        foreach ($settings as $key => $value) {
            if ($value !== null) {
                Setting::updateOrCreate(['key' => $key], ['value' => $value]);
            }
        }
    }

    /**
     * Upload an image setting (logo, favicon).
     */
    public function uploadImage(string $key, $file)
    {
        $extension = $file->getClientOriginalExtension();
        $fileName = $key . '_' . time() . '.' . $extension;
        
        // Ensure directory exists
        if (!file_exists(public_path('assets/images'))) {
            mkdir(public_path('assets/images'), 0755, true);
        }

        $file->move(public_path('assets/images'), $fileName);
        $path = 'assets/images/' . $fileName;

        Setting::updateOrCreate(['key' => $key], ['value' => $path]);

        return $path;
    }
}

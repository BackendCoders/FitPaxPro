<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingService
{
    /**
     * Get the URL for a setting image (logo, favicon, etc.).
     * 
     * @param string $key
     * @return string
     */
    public function getImageUrl(string $key): string
    {
        $value = $this->get($key);

        if ($value && file_exists(public_path($value))) {
            return asset($value);
        }

        // Fallbacks
        if ($key === 'logo') {
            return asset('assets/images/logo.png');
        }
        
        if ($key === 'favicon') {
            return asset('favicon.ico');
        }

        return 'https://ui-avatars.com/api/?name=F+P&background=6366f1&color=fff';
    }

    /**
     * Get a setting value by key.
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        // Simple caching for performance
        $settings = Cache::rememberForever('global_settings', function () {
            try {
                return Setting::pluck('value', 'key')->toArray();
            } catch (\Exception $e) {
                return [];
            }
        });

        return $settings[$key] ?? $default;
    }

    /**
     * Clear settings cache.
     */
    public function clearCache()
    {
        Cache::forget('global_settings');
    }
}

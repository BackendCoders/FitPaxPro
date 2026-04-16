<?php

namespace Modules\Admin\app\Interfaces;

interface SettingRepositoryInterface
{
    /**
     * Get all settings as a key-value pair.
     * 
     * @return array
     */
    public function getSettings();

    /**
     * Update multiple settings.
     * 
     * @param array $settings
     * @return void
     */
    public function updateSettings(array $settings);

    /**
     * Upload an image setting (logo, favicon).
     * 
     * @param string $key
     * @param \Illuminate\Http\UploadedFile $file
     * @return string Path
     */
    public function uploadImage(string $key, $file);
}

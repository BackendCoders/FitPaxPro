<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$base = 'exercise-library/imports/exercisedb_v1_sample/gifs_360x360';
$files = collect(glob(storage_path('app/public/' . $base . '/*.gif')))
    ->mapWithKeys(function ($path) use ($base) {
        return [basename($path) => $base . '/' . basename($path)];
    });

$updated = 0;
foreach (Illuminate\Support\Facades\DB::table('exercise_library_items')->select('id','image_path')->whereNotNull('image_path')->get() as $row) {
    $name = basename(str_replace('\\', '/', $row->image_path));
    if ($files->has($name)) {
        Illuminate\Support\Facades\DB::table('exercise_library_items')->where('id', $row->id)->update(['image_path' => $files[$name]]);
        $updated++;
    }
}

echo 'updated=' . $updated . PHP_EOL;

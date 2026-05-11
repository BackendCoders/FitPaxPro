<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$rows = Illuminate\Support\Facades\DB::table('exercise_library_items')
    ->select('id','exercise_name','image_path')
    ->whereNotNull('image_path')
    ->limit(10)
    ->get();

foreach ($rows as $row) {
    echo $row->exercise_name . " | " . $row->image_path . PHP_EOL;
}

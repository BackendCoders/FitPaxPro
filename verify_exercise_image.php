<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$exercise = App\Models\ExerciseLibraryItem::where('exercise_name', 'hack calf raise')->first();
echo $exercise->image_path . PHP_EOL;
echo $exercise->image_url . PHP_EOL;

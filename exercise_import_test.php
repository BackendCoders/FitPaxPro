<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

$controllerClass = 'Modules\\Admin\\app\\Http\\Controllers\\ExerciseLibraryController';
$controller = new $controllerClass();

$json = new UploadedFile('D:\machine learning\fitpaxproai\data\kaggle\exercisedb\exercisedb_v1_sample\exercises.json', 'exercises.json', 'application/json', null, true);
$gif = new UploadedFile('D:\machine learning\fitpaxproai\data\kaggle\exercisedb\exercisedb_v1_sample\gifs_360x360\2ORFMoR.gif', 'gifs_360x360\\2ORFMoR.gif', 'image/gif', null, true);

$request = Request::create('/admin/exercise-library/import', 'POST', [], [], [
    'import_files' => [$json, $gif],
    'import_paths' => ['exercises.json', 'gifs_360x360/2ORFMoR.gif'],
], []);
$request->headers->set('Accept', 'application/json');

$response = $controller->import($request);
echo $response->getContent(), PHP_EOL;

echo 'exists=' . (Illuminate\Support\Facades\Storage::disk('public')->exists('exercise-library/imports') ? 'yes' : 'no') . PHP_EOL;

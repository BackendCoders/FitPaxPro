<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

$controllerClass = 'Modules\\Admin\\app\\Http\\Controllers\\ExerciseLibraryController';
$controller = new $controllerClass();
$content = file_get_contents('D:\machine learning\fitpaxproai\data\cardio.json');
$file = UploadedFile::fake()->createWithContent('data/cardio.json', $content);
$request = Request::create('/admin/exercise-library/import', 'POST', [], [], ['import_files' => [$file], 'import_paths' => ['data/cardio.json']], []);
$request->headers->set('Accept', 'application/json');
$response = $controller->import($request);
echo $response->getContent(), PHP_EOL;

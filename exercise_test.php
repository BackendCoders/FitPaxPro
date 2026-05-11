<?php
require 'vendor/autoload.php';
$cls = 'Modules\\Admin\\app\\Http\\Controllers\\ExerciseLibraryController';
$c = new $cls();
$rm = new ReflectionMethod($c, 'parseJsonRows');
$rm->setAccessible(true);
$rows = $rm->invoke($c, file_get_contents('D:\machine learning\fitpaxproai\data\cardio.json'));
echo count($rows) . PHP_EOL;
echo $rows[0]['name'] . PHP_EOL;

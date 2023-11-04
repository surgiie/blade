<?php

use Surgiie\Blade\BladeEngine;
use Illuminate\Container\Container;
require_once __DIR__ . '/vendor/autoload.php';


$engine = new BladeEngine(new Container,  __DIR__ . '/.cache');
$engine->directive('test', fn ($expression) => "<?php echo {$expression}; ?>");
$contents = $engine->render("test.txt", ['name' => 'Surgiie', 'yes'=>true, 'dogs'=>['luffy', 'zoro', 'sanji']]);

$engine->deleteCachePath();


file_put_contents("rendered.txt", $contents);
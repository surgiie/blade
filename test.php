<?php

use Surgiie\Blade\BladeEngine;
use Illuminate\Container\Container;
require_once __DIR__ . '/vendor/autoload.php';


$engine = new BladeEngine(new Container,  __DIR__ . '/.cache');

$contents = $engine->render("test.txt", ['name' => 'Surgiie', 'yes'=>true, 'dogs'=>['luffy', 'zoro', 'sanji']]);

file_put_contents("rendered.txt", $contents);
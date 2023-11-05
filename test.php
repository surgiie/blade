<?php

use Surgiie\Blade\Blade;
use Illuminate\Container\Container;
require_once __DIR__ . '/vendor/autoload.php';


$engine = new Blade();
$engine->deleteCacheDirectory();
$engine->directive('test', fn ($expression) => "<?php echo {$expression}; ?>");

$contents = $engine->render("test.txt", [
    'name' => 'Surgiie',
    'favoriteFood' => 'Pizza',
    'dogs'=>['luffy', 'zoro', 'sanji']
]);



file_put_contents("rendered.txt", $contents);
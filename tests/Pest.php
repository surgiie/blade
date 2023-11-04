<?php

use Surgiie\Blade\Blade;
use Surgiie\Blade\Component;
use Surgiie\Blade\BladeEngine;
use Surgiie\Blade\Tests\TestCase;
use Illuminate\Filesystem\Filesystem;


uses(TestCase::class)->in(__DIR__);

function blade_test_file_path(string $path = '')
{
    return rtrim(__DIR__.'/mock'.'/'.$path);
}

function blade_tear_down(BladeEngine $blade = null)
{
    @mkdir($mockDir = blade_test_file_path());

    $fs = new Filesystem;
    $fs->deleteDirectory($mockDir);

    $fs->deleteDirectory($blade->getCachePath());
    // make sure we are on a fresh cache/resolver, to avoid collisions between tests
    // if we happen to use the same component/file names but different content.
    Component::flushCache();
    Component::forgetComponentsResolver();
}

function put_blade_test_file(string $file, string $contents)
{
    $file = trim($file, '/');

    $path = blade_test_file_path().'/'.$file;

    @mkdir(dirname($path), recursive: true);

    file_put_contents($path, $contents);
}

<?php

use Surgiie\Blade\Blade;
use Surgiie\Blade\Component;
use Surgiie\Blade\Tests\TestCase;
use Illuminate\Filesystem\Filesystem;

uses(TestCase::class)->in(__DIR__);

function testBlade()
{
    Blade::setCachePath(test_mock_path(".cache"));

    return new Blade;
}

function test_mock_path(string $path = '')
{
    return rtrim(__DIR__.'/mock'.'/'.$path);
}

function tear_down()
{
    (new Filesystem)->deleteDirectory(test_mock_path());
    // // make sure we are on a fresh cache/resolver, to avoid collisions between tests
    // // if we happen to use the same component/file names but different content.
    // Component::flushCache();
    // Component::forgetComponentsResolver();
}

function write_mock_file(string $file, string $contents)
{
    $file = trim($file, '/');

    $path = test_mock_path().'/'.$file;

    @mkdir(dirname($path), recursive: true);

    file_put_contents($path, $contents);

    return $path;
}

<?php

use Illuminate\Filesystem\Filesystem;
use Surgiie\Blade\Blade;
use Surgiie\Blade\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

function testBlade()
{
    Blade::setCachePath(test_mock_path('.cache'));

    return new Blade;
}

function test_mock_path(string $path = '')
{
    return rtrim(__DIR__.'/mock'.'/'.$path);
}

function tear_down()
{
    (new Filesystem)->deleteDirectory(test_mock_path());
}

function write_mock_file(string $file, string $contents)
{
    $file = trim($file, '/');

    $path = test_mock_path().$file;

    @mkdir(dirname($path), recursive: true);

    file_put_contents($path, $contents);

    return $path;
}

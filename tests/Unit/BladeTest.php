<?php

use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Surgiie\Blade\Blade;
use Surgiie\Blade\Exceptions\FileNotFoundException;
use Surgiie\Blade\Exceptions\UndefinedVariableException;

/**The directory we use to put test files.*/
function test_file_path(string $path = '')
{
    return rtrim(__DIR__.'/mock'.'/'.$path);
}
/**Cleanup steps.*/
function tear_down()
{
    @mkdir($mockDir = test_file_path());

    $fs = new Filesystem;

    $fs->deleteDirectory($mockDir, preserve: true);
}
/**
 * Write a test file to testing directory.
 */
function put_test_file(string $file, string $contents)
{
    $file = trim($file, '/');

    $path = test_file_path().'/'.$file;

    @mkdir(dirname($path), recursive: true);

    file_put_contents($path, $contents);
}

beforeEach(fn () => tear_down());
afterEach(fn () => tear_down());

it('throws exception when file doesnt exist', function () {
    expect(function () {
        $blade = new Blade(new Container, new Filesystem);
        $blade->compile('/something', []);
    })->toThrow(FileNotFoundException::class);
});

it('it can compile file', function () {
    put_test_file('example.yaml', <<<'EOL'
    name: {{ $name }}
    favorite_food: {{ $favoriteFood }}
    pets:
        @foreach($dogs as $dog)
        - {{ $dog }}
        @endforeach
    contact_info:
        phone: 1234567890
        @if($includeAddress)
        street_info: 123 Lane.
        @endif
    EOL);

    $blade = new Blade(new Container, new Filesystem);

    $contents = $blade->compile(test_file_path('example.yaml'), [
        'name' => 'Bob',
        'favoriteFood' => 'Pizza',
        'includeAddress' => true,
        'dogs' => ['Rex', 'Charlie'],
    ]);

    expect($contents)->toBe(<<<'EOL'
    name: Bob
    favorite_food: Pizza
    pets:
        - Rex
        - Charlie
    contact_info:
        phone: 1234567890
        street_info: 123 Lane.
    EOL);
});

it('throws exception when variables are missing', function () {
    expect(function () {
        put_test_file('example.conf', <<<'EOL'
        server {
            server_name   {{$serverName}};
            access_log   {{$accessLogPath}}  main;
            location /{{$mainEndpoint}} {
                @if($production ?? false)
                root /data/www/production
                @else
                root /data/www/staging
                @endif
            }
            @foreach($apiEndpoint as $endpoint)
            location {{$endpoint}} {
                proxy_pass  api.com{{$endpoint}};
            }
            @endforeach
        }
        EOL);

        $blade = new Blade(new Container, new Filesystem);
        $blade->compile(test_file_path('example.conf'), []);
    })->toThrow(UndefinedVariableException::class);
});

it('it can include file', function () {
    put_test_file('main.yaml', <<<'EOL'
    name: {{ $name }}
    favorite_food: {{ $favoriteFood }}
    pets:
        @foreach($dogs as $dog)
        - {{ $dog }}
        @endforeach
    @include('include.yaml')
    EOL);
    put_test_file('include.yaml', <<<'EOL'
    contact_info:
        phone: 1234567890
        @if($includeAddress)
        street_info: 123 Lane.
        @endif
    EOL);

    $blade = new Blade(new Container, new Filesystem);

    $contents = $blade->compile(test_file_path('main.yaml'), [
        'name' => 'Bob',
        'favoriteFood' => 'Pizza',
        'includeAddress' => true,
        'dogs' => ['Rex', 'Charlie'],
    ]);

    expect($contents)->toBe(<<<'EOL'
    name: Bob
    favorite_food: Pizza
    pets:
        - Rex
        - Charlie
    contact_info:
        phone: 1234567890
        street_info: 123 Lane.
    EOL);
});

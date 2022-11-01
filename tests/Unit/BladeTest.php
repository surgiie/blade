<?php

use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Surgiie\Blade\Blade;
use Surgiie\Blade\Exceptions\FileNotFoundException;

beforeEach(function () {
    $this->blade = new Blade(new Container, new Filesystem);

    blade_tear_down($this->blade);
});

// it('throws exception when file doesnt exist', function () {
//     expect(function () {
//         $this->blade->compile('/something', []);
//     })->toThrow(FileNotFoundException::class);
// });

// it('compiles variables', function () {
//     put_blade_test_file('example.txt', <<<'EOL'
//     {{$relationship}}
//     {{$name}}
//     EOL);

//     $contents = $this->blade->compile(blade_test_file_path('example.txt'), [
//         'relationship' => 'Uncle',
//         'name' => 'Bob',
//     ]);

//     expect($contents)->toBe(<<<'EOL'
//     Uncle
//     Bob
//     EOL);
// });
// it('compiles nested variables', function () {
//     put_blade_test_file('example.txt', <<<'EOL'
//     {{$name}}
//         {{$relationship}}
//     EOL);

//     $contents = $this->blade->compile(blade_test_file_path('example.txt'), [
//         'relationship' => 'Uncle',
//         'name' => 'Bob',
//     ]);

//     expect($contents)->toBe(<<<'EOL'
//     Bob
//         Uncle
//     EOL);
// });

// it('escapes variable html', function () {
//     put_blade_test_file('example.txt', <<<'EOL'
//     {{$html}}
//     EOL);

//     $contents = $this->blade->compile(blade_test_file_path('example.txt'), [
//         'html' => '<script>alert("foo")</script>',
//     ]);

//     expect($contents)->toBe(<<<'EOL'
//     &lt;script&gt;alert(&quot;foo&quot;)&lt;/script&gt;
//     EOL);
// });

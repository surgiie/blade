<?php

use Surgiie\Blade\Blade;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Surgiie\Blade\Exceptions\FileNotFoundException;


// it('can compile blade x class components via absolute path', function () {
//     $class = <<<"EOL"
//     <?php
//         namespace Surgiie\Blade\Tests;
//         use Surgiie\Blade\Component as BladeComponent;
//         class TestComponentTwo extends BladeComponent
//         {
//             public \$type;
//             public \$message;
//             public function __construct(\$type, \$message)
//             {
//                 \$this->type = \$type;
//                 \$this->message = \$message;
//             }
//             public function render()
//             {

//                 return blade()->compile(__DIR__.'/alert.txt', [
//                     'type' => \$this->type,
//                     'message' => \$this->message,
//                 ]);
//             }
//         }
//         return TestComponentTwo::class;
//     EOL;

//     write_mock_file('class-component2.php', $class);
//     write_mock_file('alert.txt', <<<'EOL'
//     {{ $type }}: {{ $message }}
//     EOL);

//     $path = ltrim(str_replace('/', '.', test_mock_path('class-component2')).'.php', '.');

//     write_mock_file('file.yaml', <<<"EOL"
//     <x--$path :type='\$type' :message='\$message' />
//     EOL);

//     $contents = testBlade()->compile(test_mock_path('file.yaml'), [
//         'message' => 'Something went right!',
//         'type' => 'success',
//     ]);

//     expect($contents)->toBe(<<<'EOL'
//     success: Something went right!
//     EOL);
// });

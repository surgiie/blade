<?php

use Surgiie\Blade\Blade;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Surgiie\Blade\Exceptions\FileNotFoundException;

// it('can compile blade x anonymous components', function () {
//     write_mock_file('component.yaml', <<<'EOL'
//     name: {{ $name }}
//     EOL);

//     write_mock_file('test.yaml', <<<'EOL'
//     <x-component.yaml :name='$name' />
//     favorite_food: {{ $favoriteFood }}
//     family_info:
//     @switch($oldest)
//     @case(1)
//         oldest_child: true
//         @break
//     @case(2)
//         oldest_child: false
//         @break
//     @endswitch
//     EOL);

//     $contents = testBlade()->compile(test_mock_path('test.yaml'), [
//         'name' => 'Bob',
//         'favoriteFood' => 'Pizza',
//         'oldest' => true,
//     ]);

//     expect($contents)->toBe(<<<'EOL'
//     name: Bob
//     favorite_food: Pizza
//     family_info:
//         oldest_child: true
//     EOL);

//     // nested example
//     write_mock_file('component2.yaml', <<<'EOL'
//     name: {{ $name }}
//     EOL);

//     write_mock_file('example2.yaml', <<<'EOL'
//         <x-component2.yaml :name='$name' />
//     favorite_food: {{ $favoriteFood }}
//     family_info:
//     @switch($oldest)
//     @case(1)
//         oldest_child: true
//         @break
//     @case(2)
//         oldest_child: false
//         @break
//     @endswitch
//     EOL);

//     $contents = testBlade()->compile(test_mock_path('example2.yaml'), [
//         'name' => 'Ricky',
//         'favoriteFood' => 'Pasta',
//         'oldest' => true,
//     ]);

//     expect($contents)->toBe(<<<'EOL'
//         name: Ricky
//     favorite_food: Pasta
//     family_info:
//         oldest_child: true
//     EOL);
// });

// it('can compile blade x anonymous components via absolute path', function () {
//     write_mock_file('component.yaml', <<<'EOL'
//     name: {{ $name }}
//     EOL);

//     $path = ltrim(str_replace('/', '.', test_mock_path('component')).'.yaml', '.');

//     write_mock_file('main.yaml', <<<"EOL"
//     <x--$path :name='\$name' />
//     family_info:
//     @switch(\$oldest)
//     @case(1)
//         oldest_child: true
//         @break
//     @case(2)
//         oldest_child: false
//         @break
//     @endswitch
//     EOL);

//     $contents = testBlade()->compile(test_mock_path('main.yaml'), [
//         'name' => 'Bob',
//         'favoriteFood' => 'Pizza',
//         'oldest' => true,
//     ]);

//     expect($contents)->toBe(<<<'EOL'
//     name: Bob
//     family_info:
//         oldest_child: true
//     EOL);
// });

// it('can compile blade x class components', function () {
//     $class = <<<"EOL"
//     <?php
//         namespace Surgiie\Blade\Tests;
//         use Surgiie\Blade\Component as BladeComponent;
//         class TestComponent extends BladeComponent
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
//         return TestComponent::class;
//     EOL;

//     write_mock_file('class-component.php', $class);
//     write_mock_file('alert.txt', <<<'EOL'
//     {{ $type }}: {{ $message }}
//     EOL);

//     write_mock_file('file.yaml', <<<'EOL'
//     <x-class-component :type='$type' :message='$message' />
//     EOL);

//     $contents = testBlade()->compile(test_mock_path('file.yaml'), [
//         'message' => 'Something went wrong!',
//         'type' => 'error',
//     ]);

//     expect($contents)->toBe(<<<'EOL'
//     error: Something went wrong!
//     EOL);
// });

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

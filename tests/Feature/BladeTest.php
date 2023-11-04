<?php

// use Illuminate\Container\Container;
// use Illuminate\Filesystem\Filesystem;
// use Surgiie\Blade\Blade;

// beforeEach(function () {
//     $this->blade = new Blade(new Container, new Filesystem);

//     blade_tear_down($this->blade);
// });
// afterAll(function () {
//     $fs = new Filesystem;
//     $fs->deleteDirectory(blade_test_file_path());
// });
// it('can compile @foreach', function () {
//     put_blade_test_file('example.yaml', <<<'EOL'
//     name: {{ $name }}
//     favorite_food: {{ $favoriteFood }}
//     pets:
//         @foreach($dogs as $dog)
//         - {{ $dog }}
//         @endforeach
//     EOL);

//     $contents = $this->blade->compile(blade_test_file_path('example.yaml'), [
//         'name' => 'Bob',
//         'favoriteFood' => 'Pizza',
//         'dogs' => ['Rex', 'Charlie'],
//     ]);

//     expect($contents)->toBe(<<<'EOL'
//     name: Bob
//     favorite_food: Pizza
//     pets:
//         - Rex
//         - Charlie
//     EOL);
//     // nested example:
//     put_blade_test_file('example2.yaml', <<<'EOL'
//     name: {{ $name }}
//     favorite_food: {{ $favoriteFood }}
//     pets:
//             @foreach($dogs as $dog)
//             - {{ $dog }}
//             @endforeach
//     EOL);

//     $contents = $this->blade->compile(blade_test_file_path('example2.yaml'), [
//         'name' => 'Bob',
//         'favoriteFood' => 'Pizza',
//         'dogs' => ['Rex', 'Charlie'],
//     ]);

//     expect($contents)->toBe(<<<'EOL'
//     name: Bob
//     favorite_food: Pizza
//     pets:
//             - Rex
//             - Charlie
//     EOL);
// });

// it('can compile @forelse', function () {
//     put_blade_test_file('example.yaml', <<<'EOL'
//     name: {{ $name }}
//     favorite_food: {{ $favoriteFood }}
//     pets:
//         @forelse($dogs as $dog)
//         - {{ $dog }}
//         @empty
//         - 'I have no dogs'
//         @endforelse
//     EOL);

//     $contents = $this->blade->compile(blade_test_file_path('example.yaml'), [
//         'name' => 'Bob',
//         'favoriteFood' => 'Pizza',
//         'dogs' => ['Rex', 'Charlie'],
//     ]);

//     expect($contents)->toBe(<<<'EOL'
//     name: Bob
//     favorite_food: Pizza
//     pets:
//         - Rex
//         - Charlie
//     EOL);

//     // nested example
//     put_blade_test_file('example2.yaml', <<<'EOL'
//     name: {{ $name }}
//     favorite_food: {{ $favoriteFood }}
//     pets:
//             @forelse($dogs as $dog)
//             - {{ $dog }}
//             @empty
//             - 'I have no dogs'
//             @endforelse
//     EOL);

//     $contents = $this->blade->compile(blade_test_file_path('example2.yaml'), [
//         'name' => 'Bob',
//         'favoriteFood' => 'Pizza',
//         'dogs' => ['Rex', 'Charlie'],
//     ]);

//     expect($contents)->toBe(<<<'EOL'
//     name: Bob
//     favorite_food: Pizza
//     pets:
//             - Rex
//             - Charlie
//     EOL);
// });

// it('can compile @for', function () {
//     put_blade_test_file('example.yaml', <<<'EOL'
//     name: {{ $name }}
//     favorite_food: {{ $favoriteFood }}
//     favorite_numbers:
//     @for ($i = 0; $i < 3; $i++)
//         - '{{ $i }}'
//     @endfor
//     EOL);

//     $contents = $this->blade->compile(blade_test_file_path('example.yaml'), [
//         'name' => 'Bob',
//         'favoriteFood' => 'Pizza',
//     ]);

//     expect($contents)->toBe(<<<'EOL'
//     name: Bob
//     favorite_food: Pizza
//     favorite_numbers:
//         - '0'
//         - '1'
//         - '2'
//     EOL);
//     // nested example
//     put_blade_test_file('example2.yaml', <<<'EOL'
//     name: {{ $name }}
//     favorite_food: {{ $favoriteFood }}
//     favorite_numbers:
//         @for ($i = 0; $i < 3; $i++)
//             - '{{ $i }}'
//         @endfor
//     EOL);

//     $contents = $this->blade->compile(blade_test_file_path('example2.yaml'), [
//         'name' => 'Bob',
//         'favoriteFood' => 'Pizza',
//     ]);

//     expect($contents)->toBe(<<<'EOL'
//     name: Bob
//     favorite_food: Pizza
//     favorite_numbers:
//             - '0'
//             - '1'
//             - '2'
//     EOL);
// });

// it('can compile @while', function () {
//     put_blade_test_file('example.yaml', <<<'EOL'
//     name: {{ $name }}
//     favorite_food: {{ $favoriteFood }}
//     favorite_numbers:
//     @php($count = 0)
//     @while ($count < 3)
//         - '{{ $count }}'
//         @php($count ++)
//     @endwhile
//     EOL);

//     $contents = $this->blade->compile(blade_test_file_path('example.yaml'), [
//         'name' => 'Bob',
//         'favoriteFood' => 'Pizza',
//     ]);

//     expect($contents)->toBe(<<<'EOL'
//     name: Bob
//     favorite_food: Pizza
//     favorite_numbers:
//         - '0'
//         - '1'
//         - '2'
//     EOL);

//     // nested
//     put_blade_test_file('example2.yaml', <<<'EOL'
//     name: {{ $name }}
//     favorite_food: {{ $favoriteFood }}
//     favorite_numbers:
//     @php($count = 0)
//         @while ($count < 3)
//             - '{{ $count }}'
//             @php($count ++)
//         @endwhile
//     EOL);

//     $contents = $this->blade->compile(blade_test_file_path('example2.yaml'), [
//         'name' => 'Bob',
//         'favoriteFood' => 'Pizza',
//     ]);

//     expect($contents)->toBe(<<<'EOL'
//     name: Bob
//     favorite_food: Pizza
//     favorite_numbers:
//             - '0'
//             - '1'
//             - '2'
//     EOL);
// });

// it('can compile @component', function () {
//     put_blade_test_file('component.yaml', <<<'EOL'
//     data: {{ $data }}
//     EOL);

//     put_blade_test_file('example.yaml', <<<'EOL'
//     name: {{ $name }}
//     favorite_food: {{ $favoriteFood }}
//     @component('component.yaml', ['data'=>'foobar'])
//     @endcomponent
//     favorite_numbers:
//     @php($count = 0)
//     @while ($count < 3)
//         - '{{ $count }}'
//         @php($count ++)
//     @endwhile
//     EOL);

//     $contents = $this->blade->compile(blade_test_file_path('example.yaml'), [
//         'name' => 'Bob',
//         'favoriteFood' => 'Pizza',
//     ]);

//     expect($contents)->toBe(<<<'EOL'
//     name: Bob
//     favorite_food: Pizza
//     data: foobar
//     favorite_numbers:
//         - '0'
//         - '1'
//         - '2'
//     EOL);
//     // nested example:
//     put_blade_test_file('component2.yaml', <<<'EOL'
//         data: {{ $data }}
//         nested: true
//     EOL);

//     put_blade_test_file('example2.yaml', <<<'EOL'
//     name: {{ $name }}
//         favorite_food: {{ $favoriteFood }}
//     @component('component2.yaml', ['data'=>'foobar'])
//     @endcomponent
//     favorite_numbers:
//     @php($count = 0)
//     @while ($count < 3)
//         - '{{ $count }}'
//         @php($count ++)
//     @endwhile
//     EOL);

//     $contents = $this->blade->compile(blade_test_file_path('example2.yaml'), [
//         'name' => 'Bob',
//         'favoriteFood' => 'Pizza',
//     ]);

//     expect($contents)->toBe(<<<'EOL'
//     name: Bob
//         favorite_food: Pizza
//         data: foobar
//         nested: true
//     favorite_numbers:
//         - '0'
//         - '1'
//         - '2'
//     EOL);
// });

// it('can compile @component via absolute path', function () {
//     $path = blade_test_file_path('component.yaml');

//     put_blade_test_file('component.yaml', <<<'EOL'
//     data: {{ $data }}
//     EOL);

//     put_blade_test_file('example.yaml', <<<"EOL"
//     name: {{ \$name }}
//     favorite_food: {{ \$favoriteFood }}
//     @component('$path', ['data'=>'foobar'])
//     @endcomponent
//     favorite_numbers:
//     @php(\$count = 0)
//     @while (\$count < 3)
//         - '{{ \$count }}'
//         @php(\$count ++)
//     @endwhile
//     EOL);

//     $contents = $this->blade->compile(blade_test_file_path('example.yaml'), [
//         'name' => 'Bob',
//         'favoriteFood' => 'Pizza',
//     ]);

//     expect($contents)->toBe(<<<'EOL'
//     name: Bob
//     favorite_food: Pizza
//     data: foobar
//     favorite_numbers:
//         - '0'
//         - '1'
//         - '2'
//     EOL);
// });

// it('can compile component @slot', function () {
//     put_blade_test_file('component.yaml', <<<'EOL'
//     data: {{ $data }}
//     {{ $format ?? 'format:yaml' }}
//     EOL);

//     put_blade_test_file('example.yaml', <<<'EOL'
//     name: {{ $name }}
//     favorite_food: {{ $favoriteFood }}
//     @component('component.yaml', ['data'=>'foobar'])
//     @slot('format')
//     format: json
//     @endslot
//     @endcomponent
//     EOL);

//     $contents = $this->blade->compile(blade_test_file_path('example.yaml'), [
//         'name' => 'Bob',
//         'favoriteFood' => 'Pizza',
//     ]);

//     expect($contents)->toBe(<<<'EOL'
//     name: Bob
//     favorite_food: Pizza
//     data: foobar
//     format: json
//     EOL);
// });

// it('can compile @if', function () {
//     put_blade_test_file('example.yaml', <<<'EOL'
//     name: {{ $name }}
//     favorite_food: {{ $favoriteFood }}
//     contact_info:
//         phone: 1234567890
//     @if($includeAddress)
//     street_info: 123 Lane.
//     @endif
//     EOL);

//     $contents = $this->blade->compile(blade_test_file_path('example.yaml'), [
//         'name' => 'Bob',
//         'favoriteFood' => 'Pizza',
//         'includeAddress' => true,
//     ]);

//     expect($contents)->toBe(<<<'EOL'
//     name: Bob
//     favorite_food: Pizza
//     contact_info:
//         phone: 1234567890
//     street_info: 123 Lane.
//     EOL);
//     // nested example:
//     put_blade_test_file('example2.yaml', <<<'EOL'
//     name: {{ $name }}
//     favorite_food: {{ $favoriteFood }}
//     contact_info:
//         phone: 1234567890
//         @if($includeAddress)
//         street_info: 123 Lane.
//         @endif
//     EOL);

//     $contents = $this->blade->compile(blade_test_file_path('example2.yaml'), [
//         'name' => 'Jeff',
//         'favoriteFood' => 'Salad',
//         'includeAddress' => true,
//     ]);

//     expect($contents)->toBe(<<<'EOL'
//     name: Jeff
//     favorite_food: Salad
//     contact_info:
//         phone: 1234567890
//         street_info: 123 Lane.
//     EOL);
// });

// it('can compile @if with @else', function () {
//     put_blade_test_file('example.yaml', <<<'EOL'
//     name: {{ $name }}
//     favorite_food: {{ $favoriteFood }}
//     phone: 1234567890
//     @if($includeAddress)
//     street_info: 123 Lane.
//     @else
//     street_info: none
//     @endif
//     EOL);

//     $contents = $this->blade->compile(blade_test_file_path('example.yaml'), [
//         'name' => 'Bob',
//         'favoriteFood' => 'Cereal',
//         'includeAddress' => false,
//     ]);

//     expect($contents)->toBe(<<<'EOL'
//     name: Bob
//     favorite_food: Cereal
//     phone: 1234567890
//     street_info: none
//     EOL);

//     // nested example
//     put_blade_test_file('example2.yaml', <<<'EOL'
//     name: {{ $name }}
//     favorite_food: {{ $favoriteFood }}
//     phone: 1234567890
//     contact_info:
//         @if($includeAddress)
//         street_info: 123 Lane.
//         @else
//         street_info: none
//         @endif
//     EOL);

//     $contents = $this->blade->compile(blade_test_file_path('example2.yaml'), [
//         'name' => 'Julia',
//         'favoriteFood' => 'Oatmeal',
//         'includeAddress' => false,
//     ]);

//     expect($contents)->toBe(<<<'EOL'
//     name: Julia
//     favorite_food: Oatmeal
//     phone: 1234567890
//     contact_info:
//         street_info: none
//     EOL);
// });

// it('can compile @include', function () {
//     put_blade_test_file('example.json', <<<'EOL'
//     {
//         "name": "{{ $name }}",
//         "favorite_food": "{{ $favoriteFood }}",
//         "contact_info": {
//             @include('include.json')
//         }
//     }
//     EOL);
//     put_blade_test_file('include.json', <<<'EOL'
//     "phone": "1234567890",
//     @if($includeAddress)
//     "street_info": "123 Lane."
//     @endif
//     EOL);

//     $contents = $this->blade->compile(blade_test_file_path('example.json'), [
//         'name' => 'Bob',
//         'favoriteFood' => 'Pizza',
//         'includeAddress' => true,
//     ]);

//     expect($contents)->toBe(<<<'EOL'
//     {
//         "name": "Bob",
//         "favorite_food": "Pizza",
//         "contact_info": {
//             "phone": "1234567890",
//             "street_info": "123 Lane."
//         }
//     }
//     EOL);
// });

// it('can compile @includeIf', function () {
//     put_blade_test_file('example.yaml', <<<'EOL'
//     name: {{ $name }}
//     favorite_food: {{ $favoriteFood }}
//     @includeIf('include.yaml')
//     EOL);
//     put_blade_test_file('include.yaml', <<<'EOL'
//     contact_info:
//         phone: 1234567890
//         @if($includeAddress)
//         street_info: 123 Lane.
//         @endif
//     EOL);

//     $contents = $this->blade->compile(blade_test_file_path('example.yaml'), [
//         'name' => 'Bob',
//         'favoriteFood' => 'Pizza',
//         'includeAddress' => true,
//     ]);

//     expect($contents)->toBe(<<<'EOL'
//     name: Bob
//     favorite_food: Pizza
//     contact_info:
//         phone: 1234567890
//         street_info: 123 Lane.
//     EOL);
//     // nested example:
//     put_blade_test_file('example2.yaml', <<<'EOL'
//         name: {{ $name }}
//         favorite_food: {{ $favoriteFood }}
//         personal_life:
//             @includeIf('contact_info.yaml')
//         EOL);

//     put_blade_test_file('contact_info.yaml', <<<'EOL'
//         contact_info:
//             phone: 1234567890
//             @if($includeAddress)
//             street_info: 123 Lane.
//             @endif
//         EOL);

//     $contents = $this->blade->compile(blade_test_file_path('example2.yaml'), [
//         'name' => 'Bob',
//         'favoriteFood' => 'Pizza',
//         'includeAddress' => true,
//     ]);

//     expect($contents)->toBe(<<<'EOL'
//         name: Bob
//         favorite_food: Pizza
//         personal_life:
//             contact_info:
//                 phone: 1234567890
//                 street_info: 123 Lane.
//         EOL);
// });

// it('can compile nested @include', function () {
//     put_blade_test_file('example.yaml', <<<'EOL'
//     name: {{ $name }}
//     favorite_food: {{ $favoriteFood }}
//         @include('include.yaml')
//     EOL);
//     put_blade_test_file('include.yaml', <<<'EOL'
//     contact_info:
//         phone: 1234567890
//         @if($includeAddress)
//         street_info: 123 Lane.
//         @endif
//     EOL);

//     $contents = $this->blade->compile(blade_test_file_path('example.yaml'), [
//         'name' => 'Bob',
//         'favoriteFood' => 'Pizza',
//         'includeAddress' => true,
//     ]);

//     expect($contents)->toBe(<<<'EOL'
//     name: Bob
//     favorite_food: Pizza
//         contact_info:
//             phone: 1234567890
//             street_info: 123 Lane.
//     EOL);

//     // nested example:
//     $includePath = blade_test_file_path('include2.yaml');

//     put_blade_test_file('example2.yaml', <<<"EOL"
//     name: {{ \$name }}
//     favorite_food: {{ \$favoriteFood }}
//     @include('$includePath')
//     EOL);
//     put_blade_test_file('include2.yaml', <<<'EOL'
//     contact_info:
//         phone: 1234567890
//         @if($includeAddress)
//         street_info: 123 Lane.
//         @endif
//     EOL);

//     $contents = $this->blade->compile(blade_test_file_path('example2.yaml'), [
//         'name' => 'Bob',
//         'favoriteFood' => 'Pizza',
//         'includeAddress' => true,
//     ]);

//     expect($contents)->toBe(<<<'EOL'
//     name: Bob
//     favorite_food: Pizza
//     contact_info:
//         phone: 1234567890
//         street_info: 123 Lane.
//     EOL);
// });

// it('can compile @switch', function () {
//     put_blade_test_file('example.yaml', <<<'EOL'
//     name: {{ $name }}
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

//     $contents = $this->blade->compile(blade_test_file_path('example.yaml'), [
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
//     put_blade_test_file('example2.yaml', <<<'EOL'
//         name: {{ $name }}
//         favorite_food: {{ $favoriteFood }}
//         family_info:
//             @switch($oldest)
//             @case(1)
//                 oldest_child: true
//                 @break
//             @case(2)
//                 oldest_child: false
//                 @break
//             @endswitch
//         EOL);

//     $contents = $this->blade->compile(blade_test_file_path('example2.yaml'), [
//         'name' => 'Bob',
//         'favoriteFood' => 'Pizza',
//         'oldest' => true,
//     ]);

//     expect($contents)->toBe(<<<'EOL'
//         name: Bob
//         favorite_food: Pizza
//         family_info:
//                 oldest_child: true
//         EOL);
// });

// it('can compile blade x anonymous components', function () {
//     put_blade_test_file('component.yaml', <<<'EOL'
//     name: {{ $name }}
//     EOL);

//     put_blade_test_file('example.yaml', <<<'EOL'
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

//     $contents = $this->blade->compile(blade_test_file_path('example.yaml'), [
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
//     put_blade_test_file('component2.yaml', <<<'EOL'
//     name: {{ $name }}
//     EOL);

//     put_blade_test_file('example2.yaml', <<<'EOL'
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

//     $contents = $this->blade->compile(blade_test_file_path('example2.yaml'), [
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
//     put_blade_test_file('component.yaml', <<<'EOL'
//     name: {{ $name }}
//     EOL);

//     $path = ltrim(str_replace('/', '.', blade_test_file_path('component')).'.yaml', '.');

//     put_blade_test_file('main.yaml', <<<"EOL"
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

//     $contents = $this->blade->compile(blade_test_file_path('main.yaml'), [
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

//     put_blade_test_file('class-component.php', $class);
//     put_blade_test_file('alert.txt', <<<'EOL'
//     {{ $type }}: {{ $message }}
//     EOL);

//     put_blade_test_file('file.yaml', <<<'EOL'
//     <x-class-component :type='$type' :message='$message' />
//     EOL);

//     $contents = $this->blade->compile(blade_test_file_path('file.yaml'), [
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

//     put_blade_test_file('class-component2.php', $class);
//     put_blade_test_file('alert.txt', <<<'EOL'
//     {{ $type }}: {{ $message }}
//     EOL);

//     $path = ltrim(str_replace('/', '.', blade_test_file_path('class-component2')).'.php', '.');

//     put_blade_test_file('file.yaml', <<<"EOL"
//     <x--$path :type='\$type' :message='\$message' />
//     EOL);

//     $contents = $this->blade->compile(blade_test_file_path('file.yaml'), [
//         'message' => 'Something went right!',
//         'type' => 'success',
//     ]);

//     expect($contents)->toBe(<<<'EOL'
//     success: Something went right!
//     EOL);
// });

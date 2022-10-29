<?php

use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Surgiie\Blade\Blade;

beforeEach(function () {
    $this->blade = new Blade(new Container, new Filesystem);

    blade_tear_down($this->blade);
});

it('can compile @foreach', function () {
    put_blade_test_file('example.yaml', <<<'EOL'
    name: {{ $name }}
    favorite_food: {{ $favoriteFood }}
    pets:
        @foreach($dogs as $dog)
        - {{ $dog }}
        @endforeach
    EOL);

    $contents = $this->blade->compile(blade_test_file_path('example.yaml'), [
        'name' => 'Bob',
        'favoriteFood' => 'Pizza',
        'dogs' => ['Rex', 'Charlie'],
    ]);

    expect($contents)->toBe(<<<'EOL'
    name: Bob
    favorite_food: Pizza
    pets:
        - Rex
        - Charlie
    EOL);
});

it('can compile @forelse', function () {
    put_blade_test_file('example.yaml', <<<'EOL'
    name: {{ $name }}
    favorite_food: {{ $favoriteFood }}
    pets:
        @forelse($dogs as $dog)
        - {{ $dog }}
        @empty
        - 'I have no dogs'
        @endforelse
    EOL);

    $contents = $this->blade->compile(blade_test_file_path('example.yaml'), [
        'name' => 'Bob',
        'favoriteFood' => 'Pizza',
        'dogs' => ['Rex', 'Charlie'],
    ]);

    expect($contents)->toBe(<<<'EOL'
    name: Bob
    favorite_food: Pizza
    pets:
        - Rex
        - Charlie
    EOL);
});
it('can compile @for', function () {
    put_blade_test_file('example.yaml', <<<'EOL'
    name: {{ $name }}
    favorite_food: {{ $favoriteFood }}
    favorite_numbers:
    @for ($i = 0; $i < 3; $i++)
        - '{{ $i }}'
    @endfor
    EOL);

    $contents = $this->blade->compile(blade_test_file_path('example.yaml'), [
        'name' => 'Bob',
        'favoriteFood' => 'Pizza',
    ]);

    expect($contents)->toBe(<<<'EOL'
    name: Bob
    favorite_food: Pizza
    favorite_numbers:
        - '0'
        - '1'
        - '2'
    EOL);
});

it('can compile @while', function () {
    put_blade_test_file('example.yaml', <<<'EOL'
    name: {{ $name }}
    favorite_food: {{ $favoriteFood }}
    favorite_numbers:
    @php($count = 0)
    @while ($count < 3)
        - '{{ $count }}'
        @php($count ++)
    @endwhile
    EOL);

    $contents = $this->blade->compile(blade_test_file_path('example.yaml'), [
        'name' => 'Bob',
        'favoriteFood' => 'Pizza',
    ]);

    expect($contents)->toBe(<<<'EOL'
    name: Bob
    favorite_food: Pizza
    favorite_numbers:
        - '0'
        - '1'
        - '2'
    EOL);
});

it('can compile @component', function () {
    put_blade_test_file('component.yaml', <<<'EOL'
    data: {{ $data }}
    EOL);

    put_blade_test_file('example.yaml', <<<'EOL'
    name: {{ $name }}
    favorite_food: {{ $favoriteFood }}
    @component('component.yaml', ['data'=>'foobar'])
    @endcomponent
    favorite_numbers:
    @php($count = 0)
    @while ($count < 3)
        - '{{ $count }}'
        @php($count ++)
    @endwhile
    EOL);

    $contents = $this->blade->compile(blade_test_file_path('example.yaml'), [
        'name' => 'Bob',
        'favoriteFood' => 'Pizza',
    ]);

    expect($contents)->toBe(<<<'EOL'
    name: Bob
    favorite_food: Pizza
    data: foobar
    favorite_numbers:
        - '0'
        - '1'
        - '2'
    EOL);
});
it('can compile @component via absolute path', function () {
    $path = blade_test_file_path('component.yaml');

    put_blade_test_file('component.yaml', <<<'EOL'
    data: {{ $data }}
    EOL);

    put_blade_test_file('example.yaml', <<<"EOL"
    name: {{ \$name }}
    favorite_food: {{ \$favoriteFood }}
    @component('$path', ['data'=>'foobar'])
    @endcomponent
    favorite_numbers:
    @php(\$count = 0)
    @while (\$count < 3)
        - '{{ \$count }}'
        @php(\$count ++)
    @endwhile
    EOL);

    $contents = $this->blade->compile(blade_test_file_path('example.yaml'), [
        'name' => 'Bob',
        'favoriteFood' => 'Pizza',
    ]);

    expect($contents)->toBe(<<<'EOL'
    name: Bob
    favorite_food: Pizza
    data: foobar
    favorite_numbers:
        - '0'
        - '1'
        - '2'
    EOL);
});
it('can compile component @slot', function () {
    put_blade_test_file('component.yaml', <<<'EOL'
    data: {{ $data }}
    {{ $format ?? 'format:yaml' }}
    EOL);

    put_blade_test_file('example.yaml', <<<'EOL'
    name: {{ $name }}
    favorite_food: {{ $favoriteFood }}
    @component('component.yaml', ['data'=>'foobar'])
    @slot('format')
    format: json
    @endslot
    @endcomponent
    EOL);

    $contents = $this->blade->compile(blade_test_file_path('example.yaml'), [
        'name' => 'Bob',
        'favoriteFood' => 'Pizza',
    ]);

    expect($contents)->toBe(<<<'EOL'
    name: Bob
    favorite_food: Pizza
    data: foobar
    format: json
    EOL);
});

it('can compile @if', function () {
    put_blade_test_file('main.yaml', <<<'EOL'
    name: {{ $name }}
    favorite_food: {{ $favoriteFood }}
    contact_info:
        phone: 1234567890
        @if($includeAddress)
        street_info: 123 Lane.
        @else
        street_info: none
        @endif
    EOL);

    $contents = $this->blade->compile(blade_test_file_path('main.yaml'), [
        'name' => 'Bob',
        'favoriteFood' => 'Pizza',
        'includeAddress' => true,
    ]);

    expect($contents)->toBe(<<<'EOL'
    name: Bob
    favorite_food: Pizza
    contact_info:
        phone: 1234567890
        street_info: 123 Lane.
    EOL);
});

it('can compile @include', function () {
    put_blade_test_file('main.yaml', <<<'EOL'
    name: {{ $name }}
    favorite_food: {{ $favoriteFood }}
    @include('include.yaml')
    EOL);
    put_blade_test_file('include.yaml', <<<'EOL'
    contact_info:
        phone: 1234567890
        @if($includeAddress)
        street_info: 123 Lane.
        @endif
    EOL);

    $contents = $this->blade->compile(blade_test_file_path('main.yaml'), [
        'name' => 'Bob',
        'favoriteFood' => 'Pizza',
        'includeAddress' => true,
    ]);

    expect($contents)->toBe(<<<'EOL'
    name: Bob
    favorite_food: Pizza
    contact_info:
        phone: 1234567890
        street_info: 123 Lane.
    EOL);
});

it('can compile @include via absolute path', function () {
    $includePath = blade_test_file_path('include.yaml');

    put_blade_test_file('main.yaml', <<<"EOL"
    name: {{ \$name }}
    favorite_food: {{ \$favoriteFood }}
    @include('$includePath')
    EOL);
    put_blade_test_file('include.yaml', <<<'EOL'
    contact_info:
        phone: 1234567890
        @if($includeAddress)
        street_info: 123 Lane.
        @endif
    EOL);

    $contents = $this->blade->compile(blade_test_file_path('main.yaml'), [
        'name' => 'Bob',
        'favoriteFood' => 'Pizza',
        'includeAddress' => true,
    ]);

    expect($contents)->toBe(<<<'EOL'
    name: Bob
    favorite_food: Pizza
    contact_info:
        phone: 1234567890
        street_info: 123 Lane.
    EOL);
});

it('can compile @switch', function () {
    put_blade_test_file('main.yaml', <<<'EOL'
    name: {{ $name }}
    favorite_food: {{ $favoriteFood }}
    family_info:
    @switch($oldest)
    @case(1)
        oldest_child: true
        @break
    @case(2)
        oldest_child: false
        @break
    @endswitch
    EOL);

    $contents = $this->blade->compile(blade_test_file_path('main.yaml'), [
        'name' => 'Bob',
        'favoriteFood' => 'Pizza',
        'oldest' => true,
    ]);

    expect($contents)->toBe(<<<'EOL'
    name: Bob
    favorite_food: Pizza
    family_info:
        oldest_child: true
    EOL);
});

it('can compile blade x anonymous components', function () {
    put_blade_test_file('component.yaml', <<<'EOL'
    name: {{ $name }}
    EOL);

    put_blade_test_file('main.yaml', <<<'EOL'
    <x-component.yaml :name='$name' />
    favorite_food: {{ $favoriteFood }}
    family_info:
    @switch($oldest)
    @case(1)
        oldest_child: true
        @break
    @case(2)
        oldest_child: false
        @break
    @endswitch
    EOL);

    $contents = $this->blade->compile(blade_test_file_path('main.yaml'), [
        'name' => 'Bob',
        'favoriteFood' => 'Pizza',
        'oldest' => true,
    ]);

    expect($contents)->toBe(<<<'EOL'
    name: Bob
    favorite_food: Pizza
    family_info:
        oldest_child: true
    EOL);
});

it('can compile blade x anonymous components via absolute path', function () {
    put_blade_test_file('component.yaml', <<<'EOL'
    name: {{ $name }}
    EOL);

    $path = ltrim(str_replace('/', '.', blade_test_file_path('component')).'.yaml', '.');

    put_blade_test_file('main.yaml', <<<"EOL"
    <x--$path :name='\$name' />
    family_info:
    @switch(\$oldest)
    @case(1)
        oldest_child: true
        @break
    @case(2)
        oldest_child: false
        @break
    @endswitch
    EOL);

    $contents = $this->blade->compile(blade_test_file_path('main.yaml'), [
        'name' => 'Bob',
        'favoriteFood' => 'Pizza',
        'oldest' => true,
    ]);

    expect($contents)->toBe(<<<'EOL'
    name: Bob
    family_info:
        oldest_child: true
    EOL);
});

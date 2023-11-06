<?php

afterAll(function () {
    tear_down();
});

it('can render @if', function () {
    $path = write_mock_file('test.yaml', <<<'EOL'
    name: {{ $name }}
    favorite_food: {{ $favoriteFood }}
    contact_info:
        phone: 1234567890
    @if($includeAddress)
    street_info: 123 Lane.
    @endif
    EOL);

    $contents = testBlade()->render($path, [
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

it('can render nested @if', function () {
    $path = write_mock_file('test.yaml', <<<'EOL'
    name: {{ $name }}
    favorite_food: {{ $favoriteFood }}
    contact_info:
        phone: 1234567890
        @if($includeAddress)
        street_info: 123 Lane.
        @endif
    EOL);

    $contents = testBlade()->render($path, [
        'name' => 'Jeff',
        'favoriteFood' => 'Salad',
        'includeAddress' => true,
    ]);

    expect($contents)->toBe(<<<'EOL'
    name: Jeff
    favorite_food: Salad
    contact_info:
        phone: 1234567890
        street_info: 123 Lane.
    EOL);
});

it('can render @else', function () {
    $path = write_mock_file('test.yaml', <<<'EOL'
    name: {{ $name }}
    favorite_food: {{ $favoriteFood }}
    phone: 1234567890
    @if($includeAddress)
    street_info: 123 Lane.
    @else
    street_info: none
    @endif
    EOL);

    $contents = testBlade()->render($path, [
        'name' => 'Bob',
        'favoriteFood' => 'Cereal',
        'includeAddress' => false,
    ]);

    expect($contents)->toBe(<<<'EOL'
    name: Bob
    favorite_food: Cereal
    phone: 1234567890
    street_info: none
    EOL);
});

it('can render nested @else', function () {
    $path = write_mock_file('test.yaml', <<<'EOL'
    name: {{ $name }}
    favorite_food: {{ $favoriteFood }}
    phone: 1234567890
    contact_info:
        @if($includeAddress)
        street_info: 123 Lane.
        @else
        street_info: none
        @endif
    EOL);

    $contents = testBlade()->render($path, [
        'name' => 'Julia',
        'favoriteFood' => 'Oatmeal',
        'includeAddress' => false,
    ]);

    expect($contents)->toBe(<<<'EOL'
    name: Julia
    favorite_food: Oatmeal
    phone: 1234567890
    contact_info:
        street_info: none
    EOL);
});



it('can render @switch', function () {
    $path = write_mock_file('test.yaml', <<<'EOL'
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

    $contents = testBlade()->render($path, [
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

it('can render nested @switch', function () {
    $path = write_mock_file('example.yaml', <<<'EOL'
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

    $contents = testBlade()->render($path, [
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


<?php

it('can render @if', function () {
    $path = write_mock_file('example.yaml', <<<'EOL'
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
    $path = write_mock_file('example.yaml', <<<'EOL'
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
    $path = write_mock_file('example.yaml', <<<'EOL'
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
    $path = write_mock_file('example2.yaml', <<<'EOL'
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
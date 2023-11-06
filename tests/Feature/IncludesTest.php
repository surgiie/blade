<?php

afterAll(function () {
    tear_down();
});

it('can render @include', function () {
    $path = write_mock_file('example.json', <<<'EOL'
    {
        "name": "{{ $name }}",
        "favorite_food": "{{ $favoriteFood }}",
        @include('include.json')
    }
    EOL);
    write_mock_file('include.json', <<<'EOL'
    "phone": "1234567890",
    @if($includeAddress)
    "street_info": "123 Lane."
    @endif
    EOL);

    $contents = testBlade()->render($path, [
        'name' => 'Bob',
        'favoriteFood' => 'Pizza',
        'includeAddress' => true,
    ]);

    expect($contents)->toBe(<<<'EOL'
    {
        "name": "Bob",
        "favorite_food": "Pizza",
        "phone": "1234567890",
        "street_info": "123 Lane."
    }
    EOL);
});

it('can render nested @include', function () {
    $path = write_mock_file('example.json', <<<'EOL'
    {
        "name": "{{ $name }}",
        "favorite_food": "{{ $favoriteFood }}",
        "contactInfo": {
            @include('include.json')
        }
    }
    EOL);
    write_mock_file('include.json', <<<'EOL'
    "phone": "1234567890",
    @if($includeAddress)
    "street_info": "123 Lane."
    @endif
    EOL);

    $contents = testBlade()->render($path, [
        'name' => 'Bob',
        'favoriteFood' => 'Pizza',
        'includeAddress' => true,
    ]);

    expect($contents)->toBe(<<<'EOL'
    {
        "name": "Bob",
        "favorite_food": "Pizza",
        "contactInfo": {
            "phone": "1234567890",
            "street_info": "123 Lane."
        }
    }
    EOL);
});

it('can render @includeIf', function () {
    $path = write_mock_file('test.yaml', <<<'EOL'
    name: {{ $name }}
    favorite_food: {{ $favoriteFood }}
    @includeIf('include.yaml')
    EOL);
    write_mock_file('include.yaml', <<<'EOL'
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
it('can render nested @includeIf', function () {

    $path = write_mock_file('example.yaml', <<<'EOL'
        name: {{ $name }}
        favorite_food: {{ $favoriteFood }}
        personal_life:
            @includeIf('contact_info.yaml')
        EOL);

    write_mock_file('contact_info.yaml', <<<'EOL'
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
        personal_life:
            contact_info:
                phone: 1234567890
                street_info: 123 Lane.
        EOL);
});

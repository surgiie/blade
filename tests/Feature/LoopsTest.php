<?php


it('can render @foreach', function () {
    $path = write_mock_file('example.yaml', <<<'EOL'
    name: {{ $name }}
    favorite_food: {{ $favoriteFood }}
    pets:
        @foreach($dogs as $dog)
        - {{ $dog }}
        @endforeach
    EOL);

    $contents = testBlade()->render($path, [
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


it('can render nested @foreach', function () {

    $path = write_mock_file('example.yaml', <<<'EOL'
    name: {{ $name }}
    favorite_food: {{ $favoriteFood }}
    pets:
            @foreach($dogs as $dog)
            - {{ $dog }}
            @endforeach
    EOL);

    $contents = testBlade()->render($path, [
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


it('can render @forelse', function () {
    $path = write_mock_file('example.yaml', <<<'EOL'
    name: {{ $name }}
    favorite_food: {{ $favoriteFood }}
    pets:
        @forelse($dogs as $dog)
        - {{ $dog }}
        @empty
        - 'I have no dogs'
        @endforelse
    EOL);

    $contents = testBlade()->render($path, [
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

it('can render nested @forelse', function () {
    $path = write_mock_file('example.yaml', <<<'EOL'
    name: {{ $name }}
    favorite_food: {{ $favoriteFood }}
    pets:
            @forelse($dogs as $dog)
            - {{ $dog }}
            @empty
            - 'I have no dogs'
            @endforelse
    EOL);

    $contents = testBlade()->render($path, [
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


it('can render @for', function () {
    $path = write_mock_file('example.yaml', <<<'EOL'
    name: {{ $name }}
    favorite_food: {{ $favoriteFood }}
    favorite_numbers:
    @for ($i = 0; $i < 3; $i++)
        - '{{ $i }}'
    @endfor
    EOL);

    $contents = testBlade()->render($path, [
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

it('can render nested @for', function () {
    $path = write_mock_file('example2.yaml', <<<'EOL'
    name: {{ $name }}
    favorite_food: {{ $favoriteFood }}
    favorite_numbers:
        @for ($i = 0; $i < 3; $i++)
            - '{{ $i }}'
        @endfor
    EOL);

    $contents = testBlade()->render($path, [
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

it('can render @while', function () {
    $path = write_mock_file('example.yaml', <<<'EOL'
    name: {{ $name }}
    favorite_food: {{ $favoriteFood }}
    favorite_numbers:
    @php($count = 0)
    @while ($count < 3)
        - '{{ $count }}'
        @php($count ++)
    @endwhile
    EOL);

    $contents = testBlade()->render($path, [
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

it('can render nested @while', function () {
    $path = write_mock_file('example2.yaml', <<<'EOL'
    name: {{ $name }}
    favorite_food: {{ $favoriteFood }}
    favorite_numbers:
    @php($count = 0)
        @while ($count < 3)
            - '{{ $count }}'
            @php($count ++)
        @endwhile
    EOL);

    $contents = testBlade()->render($path, [
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
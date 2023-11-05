<?php

afterAll(function () {
    tear_down();
});

it('can render @component', function () {
    write_mock_file('component.yaml', <<<'EOL'
    data: {{ $data }}
    EOL);

    $path = write_mock_file('test.yaml', <<<'EOL'
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

    $contents = testBlade()->render($path, [
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

it('can render nested @component', function () {

    write_mock_file('component.yaml', <<<'EOL'
    data: {{ $data }}
    nested: true
    EOL);

    $path = write_mock_file('test.yaml', <<<'EOL'
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

    $contents = testBlade()->render($path, [
        'name' => 'Bob',
        'favoriteFood' => 'Pizza',
    ]);

    expect($contents)->toBe(<<<'EOL'
    name: Bob
        favorite_food: Pizza
        data: foobar
        nested: true
    favorite_numbers:
        - '0'
        - '1'
        - '2'
    EOL);
});

it('can render @component via absolute path', function () {
    $component = write_mock_file('component.yaml', <<<'EOL'
    data: {{ $data }}
    EOL);

    $file = write_mock_file('test.yaml', <<<"EOL"
    name: {{ \$name }}
    favorite_food: {{ \$favoriteFood }}
    @component('$component', ['data'=>'foobar'])
    @endcomponent
    favorite_numbers:
    @php(\$count = 0)
    @while (\$count < 3)
        - '{{ \$count }}'
        @php(\$count ++)
    @endwhile
    EOL);

    $contents = testBlade()->render($file, [
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

it('can render component @slot', function () {
    $component = write_mock_file('component.yaml', <<<'EOL'
    data: {{ $data }}
    {{ $format ?? 'format: yaml' }}
    EOL);

    $file = write_mock_file('test.yaml', <<<"EOL"
    name: {{ \$name }}
    favorite_food: {{ \$favoriteFood }}
    @component('$component', ['data'=>'foobar'])
    @slot('format')
    format: json
    @endslot
    @endcomponent
    EOL);

    $contents = testBlade()->render($file, [
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

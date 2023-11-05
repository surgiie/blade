<?php

afterAll(function () {
    tear_down();
});

it('renders variables', function () {
    $path = write_mock_file('example.txt', <<<'EOL'
    {{$relationship}}
    {{$name}}
    EOL);

    $contents = testBlade()->render($path, [
        'relationship' => 'Uncle',
        'name' => 'Bob',
    ]);

    expect($contents)->toBe(<<<'EOL'
    Uncle
    Bob
    EOL);
});

it('renders nested variables', function () {
    $path = write_mock_file('example.txt', <<<'EOL'
    {{$name}}
        {{$relationship}}
    EOL);

    $contents = testBlade()->render($path, [
        'relationship' => 'Uncle',
        'name' => 'Bob',
    ]);

    expect($contents)->toBe(<<<'EOL'
    Bob
        Uncle
    EOL);
});

it('escapes variable html', function () {
    $path = write_mock_file('example.txt', <<<'EOL'
    {{$html}}
    EOL);

    $contents = testBlade()->render($path, [
        'html' => '<script>alert("foo")</script>',
    ]);

    expect($contents)->toBe(<<<'EOL'
    &lt;script&gt;alert(&quot;foo&quot;)&lt;/script&gt;
    EOL);
});
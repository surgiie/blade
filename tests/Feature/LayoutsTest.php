<?php

afterAll(function () {
    tear_down();
});

it('compiles layout and yields', function () {

    $path = write_mock_file('file', <<<'EOL'
    @extends("layout")
    @section("content")
    {{ $title }}
    @endsection
    EOL);

    write_mock_file('layout', <<<'EOL'
    @yield("content")
    EOL);

    $contents = testBlade()->render($path, [
        'title' => $title = 'Hello World!',
    ]);

    expect($contents)->toBe($title);
});

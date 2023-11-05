<?php

it('respects escaped directives', function () {
    write_mock_file('example.txt', <<<'EOL'
    {{$name}}
    @@if(true)
        example
    @@endif
    EOL);

    $contents = testBlade()->render(test_mock_path('example.txt'), [
        'name' => 'Bob',
    ]);

    expect($contents)->toBe(<<<'EOL'
    Bob
    @if(true)
        example
    @endif
    EOL);
});
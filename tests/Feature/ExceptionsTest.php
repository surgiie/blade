<?php

use Surgiie\Blade\Exceptions\FileNotFoundException;
use Surgiie\Blade\Exceptions\UnresolvableException;

afterAll(function () {
    tear_down();
});

it('throws exception when file doesnt exist', function () {
    expect(function () {
        testBlade()->render('/something', []);
    })->toThrow(FileNotFoundException::class);
});


it('throws exception when class for component doesnt exist.', function () {
    expect(function () {
        write_mock_file('alert.txt', <<<'EOL'
        {{ $type }}: {{ $message }}
        EOL);

        write_mock_file('file.yaml', <<<'EOL'
        <x-test :type='$type' :message='$message' />
        EOL);

        testBlade()->render(test_mock_path('file.yaml'), [
            'message' => 'Something went wrong!',
            'type' => 'error',
        ]);

    })->toThrow(UnresolvableException::class);
});

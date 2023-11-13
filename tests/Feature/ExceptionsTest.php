<?php

use Surgiie\Blade\Exceptions\FileException;

afterAll(function () {
    tear_down();
});

it('throws exception when file doesnt exist', function () {
    expect(function () {
        testBlade()->render('/something', []);
    })->toThrow(FileException::class);
});

it('throws exception when class for component doesnt exist.', function () {
    expect(function () {

        $path = write_mock_file('file.yaml', <<<'EOL'
        <x-dont-exist :type='$type' :message='$message' />
        EOL);

        testBlade()->render($path, [
            'message' => 'Something went wrong!',
            'type' => 'error',
        ]);

    })->toThrow(FileException::class);
});

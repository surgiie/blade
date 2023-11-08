<?php

use Surgiie\Blade\Exceptions\FileNotFoundException;

afterAll(function () {
    tear_down();
});

it('throws exception when file doesnt exist', function () {
    expect(function () {
        testBlade()->render('/something', []);
    })->toThrow(FileNotFoundException::class);
});


it('throws exception when class for component doesnt exist.', function () {
    dd("TODO exception test.");
});

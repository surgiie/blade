<?php

use Surgiie\Blade\Blade;
use Surgiie\Blade\Exceptions\FileNotFoundException;



it('throws exception when file doesnt exist', function () {
    expect(function () {
        testBlade()->render('/something', []);
    })->toThrow(FileNotFoundException::class);
});
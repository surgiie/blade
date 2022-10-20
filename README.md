# Blade

![tests](https://github.com/surgiie/blade/actions/workflows/tests.yml/badge.svg)

An extended version of the Laravel Blade engine so that it can be used on any textual files.

## Installation

`composer require surgiie/blade`

### Use

```php
<?php

use Surgiie\Blade\Blade;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;

$blade = new Blade(
    container: new Container,
    filesystem: new Filesystem,
);

$contents = $blade->compile("/path/to/file", ['var'=>'example']);

```

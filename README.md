# Blade

![tests](https://github.com/surgiie/blade/actions/workflows/tests.yml/badge.svg)

An extended version of the Laravel Blade engine so that it can be used on any textual files.

## Installation

```bash
composer require surgiie/blade
```

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
### Using Components

Blade `x-*` components are supported, but since this package is customized to allow compiling file on the fly, it does require the class file for your component to return the
class constants so that the package can `require` and the class can be defined for the blade engine to `new` up and render and the class must extend `Surgiie\Blade\Component`, here's an example:

```php
<?php

use Surgiie\Blade\Blade;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;



/*

/example.txt contents:
<x-components.alert :message="var" />

*/
$contents = $blade->compile("/example.txt", ['var'=>'example']);

```


Where `components.alert` means either a class php file exists as `components/alert.php` exists and it returns the class constant:


```php
<?php

namespace Components;

use Surgiie\Blade\Component as BladeComponent;

class Alert extends BladeComponent
{
    public $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function render()
    {
        return blade()->compile('alert.txt', [
            'message' => $this->message,
        ]);
    }
}

return Alert::class;

```

Or `components/alert` file exists in with the raw content for the anonymous component.

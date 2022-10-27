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

// use absolute path to file
$contents = $blade->compile("/path/to/file", ['var'=>'example']);

```
### Using Components

Blade `x-*` components are supported, but since this package is customized to allow compiling any file on the fly, it behaves slightly different:

* If the component path points to a php file, i.e `/example/foo/file.php` for a class based component, then the component class, must:
    * Return the class constant so that the package can `require` and the class can be defined for the blade engine to `new` up and render.
    * Extend `Surgiie\Blade\Component` class provided by this package.

* Component tag names are treated as relative to the file being rendered, unless you use a double dash in the component tag name, i.e `<x--some.path>`, in which case the path will be treated as an absolute path, in this case `/some/path` instead of `some/path` which would be relative to whatever file you are compiling.

here's an class component example example:

```php
<?php

use Surgiie\Blade\Blade;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;

/*

/example.txt contents:
<x-components.alert :message="var" />
*/

$blade = new Blade(new Container, $fs = new Filesystem)
$contents = $blade->compile("/example.txt", ['var'=>'example']);

// cleanup files when done compiling files
$fs->deleteDirectory($blade->getCompiledPath());

```



```php
/*

In "/":

components/alert.php
example.txt // the file being rendered.

*/
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

return Alert::class; // required

```

#### Anonymous Copmponent

In the above example of `<x-components.alert />`, if the `components/alert.php` file of the component path doesnt exist, it will assume a raw file of `components/alert` exists with the raw content for the
file to be rendered as anonymous component.

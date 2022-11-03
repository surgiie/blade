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

You may also use Blade `x-*` components in your file:


#### Anonymous Components
```html
<x-components.example data="Something" />
```

Where `components.example` is a relative file  `components/example` to the file being compiled, this file can then contain any raw content and will be treated as a anonymous component.

#### Class Components

Class based components have no special difference in syntax:
```html
<x-components.example data="Something" />
```

The only difference here is that `components.example` is a relative `.php` file  to the file being compiled, in this case `components/example.php`.


**Note** that since this package is customized to allow compiling any file path on the fly, the `.php` class component, must return the `::class` component, and extend the `\Surgiie\Blade\Component` class:

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
        return blade()->compile('/your-component-file', [
            'message' => $this->message,
        ]);
    }
}

return Alert::class; // required so the engine can require the class on the fly and remember it.
```

#### Using absolute paths for components:

Components are resolved using a relative path from the filed being compiled, if you want to use an absolute path, use a double `-` in the component name:

```html
<x--components.example data="Something" />
```

This will resolve the path to look for the file to `/components/example` instead of `/<file-being-compiled-path>/components/example`.
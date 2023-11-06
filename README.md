# Blade

![tests](https://github.com/surgiie/blade/actions/workflows/tests.yml/badge.svg)

An extended standalone version of the Laravel Blade engine so that it can be used on any textual file.

## Why?

There are several standalone blade engines out there, but there all meant for html files where spacing is not important.

I wanted the ability to use the blade engine for rendering template files such as yaml and wanted it to work basically on any textual file on the fly.

The blade engine trims output buffer and some compiled directives dont preserve nesting of the rendered content, for example, if you have a file like this:

```yaml
# example.yaml
name: {{ $name }}

    @include("partial.yaml")
```
Each line of the contents of the `@include` should also be indented by the number of spaces left of the `@include` directive, but it's not.

This is a problem, because the rendered result will not match the original file structure in terms of nesting/spacing, which is problematic when rendering files like `.yaml`

where nesting is semantically important.

## Installation

```bash
composer require surgiie/blade
```

### Use

```php
<?php

use Surgiie\Blade\Blade;
use Illuminate\Container\Container;

// set a cache directory for compiled cache files, defaults to vendor/surgiie/blade/.cache
Blade::setCachePath("/tmp/.blade");

$blade = new Blade(
    // pass optional container, defaults to: Container::getInstance() or new instance.
    container: new Container,
);

// then render any textual file by path and vars:
$contents = $blade->render("/path/to/file", ['var'=>'example']);
```

### Delete Cached Files
You can delete cached files using the `deleteCacheDirectory` method:

```php

Blade::deleteCacheDirectory();
```

**Tip** - Consider doing this before rendering to force render files.


### Custom Directives

You can create a custom blade directive using the `directive` method:


```php
$blade = new Blade();

$blade->directive('echo', fn ($expression) => "<?php echo {$expression}; ?>");

$contents = $blade->render("/example.txt", ['name' => 'Surgiie', 'dogs'=>['luffy', 'zoro', 'sanji']]);

```

<!-- ### Using Components

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

This will resolve the path to look for the file to `/components/example` instead of `/<file-being-compiled-path>/components/example`. -->
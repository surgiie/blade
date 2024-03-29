# Blade

# Abandoned

This package is no longer maintained consider using [laravel-blade-cli](https://github.com/surgiie/laravel-blade-cli) for templating needs. 

![tests](https://github.com/surgiie/blade/actions/workflows/tests.yml/badge.svg)

An extended standalone version of the Laravel Blade engine so that it can be used on any textual file on the fly.

## Why?

There are several standalone blade packages out there, but there all meant for html template files where spacing is not important. I wanted the ability to use the blade engine for rendering template files such as yaml during my deployment ci pipelines, and wanted it to work basically on any textual file on the fly. The blade engine trims the output and some compiled directives dont preserve nesting of the rendered content, for example, if you have a file like this:

```yaml
# example.yaml
name: {{ $name }}
test:
    @include("partial.yaml")
```
Each line of the contents of the `@include` should also be indented by the number of spaces left of the `@include` directive, but it's not and the rendered result will not match the original file structure in terms of nesting/spacing. This is a problematic when rendering files like yaml where spacing and indentation are semantically important.

## Installation

```bash
composer require surgiie/blade
```

### Use

```php
<?php

use Surgiie\Blade\Blade;
use Illuminate\Container\Container;
use Surgiie\Blade\Component;

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

**Tip** - Do this before calling `render` method to force render a file.


### Custom Directives

You can create a custom blade directive using the `directive` method:


```php
$blade = new Blade();

$blade->directive('echo', fn ($expression) => "<?php echo {$expression}; ?>");

$contents = $blade->render("/example.txt", ['name' => 'Surgiie', 'dogs'=>['luffy', 'zoro', 'sanji']]);
```
### Using Components

You may also use Blade `x-*` components in your file:

[Learn More](https://laravel.com/docs/10.x/blade#components)

#### Anonymous Components

Using dot notation component tag names, you can specify a component file to render:

```html
<x-component.yaml data="Something" />
```

Where `component.yaml` resolves to the file `components/yaml` or `component.yaml` file that is relative to the file being rendered, this file can then contain any raw content and will be treated as a anonymous component.

**Absolute Paths**:
If you want to render a component file using absolute path, use a double dash instead of single dash after the `x` in tag name, i.e `x--` instead of `x-`:

```html
<x--components.foo.yaml data="Something" />
```
The above component will resolve to `/components/foo/yaml`, if that doesnt exist, resolves to `/components/foo.yaml` or errors out if either dont exist.

#### Class Components

To specify what component class to use for a component name, you can register the component using the `components` method:


```php
Blade::components([
    'components.example' => App\Components\Alert::class,
]);

```
Then you can use the component in your file:

```html
<x-components.example data="example" />
```

The engine, will then use the class to render the component.


If you are using this package where a class may not be available at runtime or want to `require` the class on the fly you can use a php file that returns the class constant:

```php
use Surgiie\Blade\Component;

class Alert extends Component;
{
   /** ....*/
}

return Alert::class;
```
Then if the component name ends with .php, the engine will attempt to `require` it on the fly:

```html
<x-alert.php />
```


<?php

use Surgiie\Blade\Blade;
use Surgiie\Blade\Component;

afterAll(function () {
    tear_down();
});

it('can render @component', function () {
    write_mock_file('component.yaml', <<<'EOL'
    data: {{ $data }}
    EOL);

    $path = write_mock_file('test.yaml', <<<'EOL'
    name: {{ $name }}
    favorite_food: {{ $favoriteFood }}
    @component('component.yaml', ['data'=>'foobar'])
    @endcomponent
    favorite_numbers:
    @php($count = 0)
    @while ($count < 3)
        - '{{ $count }}'
        @php($count ++)
    @endwhile
    EOL);

    $contents = testBlade()->render($path, [
        'name' => 'Bob',
        'favoriteFood' => 'Pizza',
    ]);

    expect($contents)->toBe(<<<'EOL'
    name: Bob
    favorite_food: Pizza
    data: foobar
    favorite_numbers:
        - '0'
        - '1'
        - '2'
    EOL);
});

it('can render nested @component', function () {
    write_mock_file('component.yaml', <<<'EOL'
    data: {{ $data }}
    nested: true
    EOL);

    $path = write_mock_file('test.yaml', <<<'EOL'
    name: {{ $name }}
        favorite_food: {{ $favoriteFood }}
        @component('component.yaml', ['data'=>'foobar'])
        @endcomponent
    favorite_numbers:
    @php($count = 0)
    @while ($count < 3)
        - '{{ $count }}'
        @php($count ++)
    @endwhile
    EOL);

    $contents = testBlade()->render($path, [
        'name' => 'Bob',
        'favoriteFood' => 'Pizza',
    ]);

    expect($contents)->toBe(<<<'EOL'
    name: Bob
        favorite_food: Pizza
        data: foobar
        nested: true
    favorite_numbers:
        - '0'
        - '1'
        - '2'
    EOL);
});

it('can render @component via absolute path', function () {
    $component = write_mock_file('component.yaml', <<<'EOL'
    data: {{ $data }}
    EOL);

    $file = write_mock_file('test.yaml', <<<"EOL"
    name: {{ \$name }}
    favorite_food: {{ \$favoriteFood }}
    @component('$component', ['data'=>'foobar'])
    @endcomponent
    favorite_numbers:
    @php(\$count = 0)
    @while (\$count < 3)
        - '{{ \$count }}'
        @php(\$count ++)
    @endwhile
    EOL);

    $contents = testBlade()->render($file, [
        'name' => 'Bob',
        'favoriteFood' => 'Pizza',
    ]);

    expect($contents)->toBe(<<<'EOL'
    name: Bob
    favorite_food: Pizza
    data: foobar
    favorite_numbers:
        - '0'
        - '1'
        - '2'
    EOL);
});

it('can render component @slot', function () {
    $component = write_mock_file('component.yaml', <<<'EOL'
    data: {{ $data }}
    {{ $format ?? 'format: yaml' }}
    EOL);

    $file = write_mock_file('test.yaml', <<<"EOL"
    name: {{ \$name }}
    favorite_food: {{ \$favoriteFood }}
    @component('$component', ['data'=>'foobar'])
    @slot('format')
    format: json
    @endslot
    @endcomponent
    EOL);

    $contents = testBlade()->render($file, [
        'name' => 'Bob',
        'favoriteFood' => 'Pizza',
    ]);

    expect($contents)->toBe(<<<'EOL'
    name: Bob
    favorite_food: Pizza
    data: foobar
    format: json
    EOL);
});

it('can render blade x anonymous components', function () {
    write_mock_file('component', <<<'EOL'
    name: {{ $name }}
    EOL);

    $path = write_mock_file('test.yaml', <<<'EOL'
    <x-component :name='$name' />
    favorite_food: {{ $favoriteFood }}
    family_info:
    @switch($oldest)
    @case(1)
        oldest_child: true
        @break
    @case(2)
        oldest_child: false
        @break
    @endswitch
    EOL);

    $contents = testBlade()->render($path, [
        'name' => 'Bob',
        'favoriteFood' => 'Pizza',
        'oldest' => true,
    ]);

    expect($contents)->toBe(<<<'EOL'
    name: Bob
    favorite_food: Pizza
    family_info:
        oldest_child: true
    EOL);
});

it('can render nested blade x anonymous components', function () {

    write_mock_file('component.yaml', <<<'EOL'
    name: {{ $name }}
    EOL);

    $path = write_mock_file('example.yaml', <<<'EOL'
        <x-component.yaml :name='$name' />
        <x-component.yaml :name='$nameTwo' />
    favorite_food: {{ $favoriteFood }}
    family_info:
    @switch($oldest)
    @case(1)
        oldest_child: true
        @break
    @case(2)
        oldest_child: false
        @break
    @endswitch
    EOL);

    $contents = testBlade()->render($path, [
        'name' => 'Ricky',
        'nameTwo' => 'Bob',
        'favoriteFood' => 'Pasta',
        'oldest' => true,
    ]);

    expect($contents)->toBe(<<<'EOL'
        name: Ricky
        name: Bob
    favorite_food: Pasta
    family_info:
        oldest_child: true
    EOL);
});

it('can render blade x anonymous components via absolute path', function () {
    write_mock_file('component.yaml', <<<'EOL'
    name: {{ $name }}
    EOL);

    $component = ltrim(str_replace('/', '.', test_mock_path('component')).'.yaml', '.');
    $path = write_mock_file('main.yaml', <<<"EOL"
    <x--$component :name='\$name' />
    family_info:
    @switch(\$oldest)
    @case(1)
        oldest_child: true
        @break
    @case(2)
        oldest_child: false
        @break
    @endswitch
    EOL);

    $contents = testBlade()->render($path, [
        'name' => 'Bob',
        'favoriteFood' => 'Pizza',
        'oldest' => true,
    ]);

    expect($contents)->toBe(<<<'EOL'
    name: Bob
    family_info:
        oldest_child: true
    EOL);
});

it('can render nested blade x anonymous components via absolute path', function () {
    write_mock_file('component.yaml', <<<'EOL'
    name: {{ $name }}
    EOL);

    $component = ltrim(str_replace('/', '.', test_mock_path('component')).'.yaml', '.');
    $path = write_mock_file('main.yaml', <<<"EOL"
        <x--$component :name='\$name' />
    family_info:
    @switch(\$oldest)
    @case(1)
        oldest_child: true
        @break
    @case(2)
        oldest_child: false
        @break
    @endswitch
    EOL);

    $contents = testBlade()->render($path, [
        'name' => 'Bob',
        'favoriteFood' => 'Pizza',
        'oldest' => true,
    ]);

    expect($contents)->toBe(<<<'EOL'
        name: Bob
    family_info:
        oldest_child: true
    EOL);
});

it('can render blade x class components', function () {
    class TestComponent extends Component
    {
        public $type;

        public $message;

        public function __construct($type, $message)
        {
            $this->type = $type;
            $this->message = $message;
        }

        public function render()
        {
            return blade()->render(test_mock_path('/alert.txt'), [
                'type' => $this->type,
                'message' => $this->message,
            ]);
        }
    }
    class TestTwoComponent extends Component
    {
        public $type;

        public $message;

        public function __construct($type, $message)
        {
            $this->type = $type;
            $this->message = $message;
        }

        public function render()
        {
            return blade()->render(test_mock_path('/alert2.txt'), [
                'type' => $this->type,
                'message' => $this->message,
            ]);
        }
    }

    Blade::components([
        'test' => TestComponent::class,
        'test-two' => TestTwoComponent::class,
    ]);

    write_mock_file('alert.txt', <<<'EOL'
    {{ $type }}: {{ $message }}
    EOL);

    write_mock_file('alert2.txt', <<<'EOL'
    {{ $type }}: {!! $message !!}
    EOL);

    write_mock_file('file.yaml', <<<'EOL'
    <x-test :type='$type' :message='$message' />
    <x-test-two :type='$typeTwo' :message='$messageTwo' />
    EOL);

    $contents = testBlade()->render(test_mock_path('file.yaml'), [
        'message' => 'Something went wrong!',
        'type' => 'error',
        'messageTwo' => "I'll let it slide this time.",
        'typeTwo' => 'warning',
    ]);

    expect($contents)->toBe(<<<'EOL'
    error: Something went wrong!
    warning: I'll let it slide this time.
    EOL);
});

it('can render blade x class components on the fly', function () {
    $view = write_mock_file('component.txt', <<<'EOL'
    {{ $type }}: {{ $message }}
    EOL);

    $class = <<<"EOL"
    <?php
        namespace Surgiie\Blade\Tests;
        use Surgiie\Blade\Component as BladeComponent;
        class TestComponent extends BladeComponent
        {
            public \$type;
            public \$message;
            public function __construct(\$type, \$message)
            {
                \$this->type = \$type;
                \$this->message = \$message;
            }
            public function render()
            {
                return blade()->render("$view", [
                    'type' => \$this->type,
                    'message' => \$this->message,
                ]);
            }
        }
        return TestComponent::class;
    EOL;

    write_mock_file('component.php', $class);

    $path = write_mock_file('file.yaml', <<<'EOL'
    <x-component.php :type='$type' :message='$message' />
    EOL);

    $contents = testBlade()->render($path, [
        'message' => 'Something went wrong!',
        'type' => 'error',
    ]);

    expect($contents)->toBe(<<<'EOL'
    error: Something went wrong!
    EOL);
});

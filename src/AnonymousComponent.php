<?php

namespace Surgiie\Blade;

use Surgiie\Blade\Exceptions\UnresolvableException;

class AnonymousComponent extends Component
{
    protected string $view;

    protected array $data = [];

    public function __construct(string $view, array $data)
    {
        $this->view = $view;
        $this->data = $data;
    }

    public static function resolve($data)
    {
        if (isset($data['view']) && ! is_file($data['view']) && ! class_exists($data['view'])) {
            throw new UnresolvableException("Could not resolve component class or file for: {$data['view']}");
        }

        return parent::resolve($data);
    }

    public function render(): string
    {
        return blade()->render($this->view, $this->data);
    }

    public function data(): array
    {
        $this->attributes = $this->attributes ?: $this->newAttributeBag();

        return array_merge(
            ($this->data['attributes'] ?? null)?->getAttributes() ?: [],
            $this->attributes->getAttributes(),
            $this->data,
            ['attributes' => $this->attributes]
        );
    }
}

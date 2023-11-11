<?php

namespace Surgiie\Blade;

class AnonymousComponent extends Component
{
    protected string $view;

    protected array $data = [];

    public function __construct(string $view, array $data)
    {
        $this->view = $view;
        $this->data = $data;
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

<?php

namespace Surgiie\Blade;

class AnonymousComponent extends Component
{
    /**
     * The component view.
     */
    protected string $view;

    /**
     * The component data.
     */
    protected array $data = [];

    public function __construct(string $view, array $data)
    {
        $this->view = $view;
        $this->data = $data;
    }

    /**
     * Get the view contents that represent the component.
     */
    public function render(): string
    {
        return blade()->compile($this->view, $this->data);
    }

    /**
     * Get the data that should be supplied to the view.
     */
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

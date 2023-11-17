<?php

namespace Surgiie\Blade;

class AnonymousComponent extends Component
{
    /**
     * The path to the file being rendered.
     */
    protected string $view;

    /**
     * The array of file data.
     */
    protected array $data = [];

    /**
     * Construct a new AnonymousComponent instance.
     */
    public function __construct(string $view, array $data)
    {
        $this->view = $view;
        $this->data = $data;
    }

    /**
     * Render the set file with the set data.
     */
    public function render(): string
    {
        return blade()->render($this->view, $this->data);
    }

    /**
     * Return the data for the component class.
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

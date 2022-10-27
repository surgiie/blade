<?php


use Surgiie\Blade\Blade;
use Surgiie\Blade\Component as BladeComponent;

return class extends BladeComponent
{
    /**
     * The alert type.
     *
     * @var string
     */
    public $type;

    /**
     * The alert message.
     *
     * @var string
     */
    public $message;

    /**
     * Create the component instance.
     *
     * @param  string  $type
     * @param  string  $message
     * @return void
     */
    public function __construct($type, $message)
    {
        $this->type = $type;
        $this->message = $message;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|\Closure|string
     */
    public function render()
    {
        return Blade::getInstance()->compile('alert.txt', [
            'type' => $this->type,
            'message' => $this->message,
        ]);
    }
}

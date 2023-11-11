<?php

use Surgiie\Blade\Component;

class Alert extends Component
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
        return blade()->render('alert.txt', [
            'type' => $this->type,
            'message' => $this->message,
        ]);
    }
}

return Alert::class;

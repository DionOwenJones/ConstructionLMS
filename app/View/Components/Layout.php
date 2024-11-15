<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Layout extends Component
{
    public function __construct(
        public ?string $title = null
    ) {}

    public function render()
    {
        return view('components.layout');
    }
}

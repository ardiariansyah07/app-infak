<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Breadcrumb extends Component
{
    public array $items;

    public function __construct(array $items)
    {
        $this->items = $items;
    }

    public function render(): View|Closure|string
    {
        return view('components.breadcrumb');
    }
}

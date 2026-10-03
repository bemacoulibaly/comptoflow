<?php

namespace App\View\Components;

use Illuminate\View\Component;

class NavItem extends Component
{
    public function __construct(
        public string $route,
        public string $icon = 'ti-circle',
    ) {}

    public function isActive(): bool
    {
        return request()->routeIs($this->route . '*');
    }

    public function render()
    {
        return view('components.nav-item');
    }
}

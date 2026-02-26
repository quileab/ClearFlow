<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AppBrand extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return <<<'HTML'
                <a href="/" wire:navigate>
                    <!-- Hidden when collapsed -->
                    <div {{ $attributes->class(["hidden-when-collapsed"]) }}>
                        <div class="flex items-center gap-2 w-fit">
                            <x-icon name="o-presentation-chart-line" class="w-8 -mb-1 text-[#11998e]" />
                            <span class="font-bold text-3xl me-3 bg-clip-text text-transparent" style="background-image: linear-gradient(to right, #11998e, #0072ff)">
                                {{ config('app.name') }}
                            </span>
                        </div>
                    </div>

                    <!-- Display when collapsed -->
                    <div class="display-when-collapsed hidden mx-5 mt-5 mb-1 h-[28px]">
                        <x-icon name="s-presentation-chart-line" class="w-8 -mb-1 text-[#11998e]" />
                    </div>
                </a>
            HTML;
    }
}

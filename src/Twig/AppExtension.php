<?php

namespace App\Twig;

use App\Helper\AppHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function __construct(private readonly AppHelper $helper)
    {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('trending_icon', $this->helper->getTrendingIcon(...), ['is_safe' => ['html']]),
            new TwigFilter('trending_color', $this->helper->getTrendingColor(...), ['is_safe' => ['html']]),
            new TwigFilter('unit', $this->helper->renderUnit(...)),
            new TwigFilter('percent', $this->helper->renderPercent(...)),
        ];
    }
}

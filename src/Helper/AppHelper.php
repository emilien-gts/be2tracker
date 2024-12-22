<?php

namespace App\Helper;

use App\Utils\MathUtils;
use Symfony\UX\Icons\IconRendererInterface;

class AppHelper
{
    public function __construct(
        private readonly IconRendererInterface $iconRenderer,
    ) {
    }

    public function getTrendingIcon(string $value = '0.00', array $attributes = []): string
    {
        $name = match (true) {
            MathUtils::eq($value, '0') => 'heroicons:arrow-path-rounded-square',
            MathUtils::gt($value, '0') => 'heroicons:arrow-trending-up',
            default => 'heroicons:arrow-trending-down',
        };

        return $this->iconRenderer->renderIcon($name, $attributes);
    }

    public function getTrendingColor(string $value = '0.00'): string
    {
        return match (true) {
            MathUtils::gt($value, '0') => 'green',
            MathUtils::eq($value, '0') => 'gray',
            default => 'red',
        };
    }

    public function renderUnit(string $value, int $scale = 2): string
    {
        return MathUtils::add($value, '0', $scale).' u';
    }

    public function renderPercent(string $value, int $scale = 2): string
    {
        return MathUtils::add($value, '0', $scale).' %';
    }
}

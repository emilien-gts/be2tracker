<?php

namespace App\Model;

class BetCombinationItem
{
    public ?string $name = null;
    public string $odd = '0.00';
    public string $stake = '0.00';

    public function __toString(): string
    {
        return (string) $this->name;
    }
}

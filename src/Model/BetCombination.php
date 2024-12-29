<?php

namespace App\Model;

use App\Enum\BetCombinationMethodEnum;
use Symfony\Component\Validator\Constraints as Assert;

class BetCombination
{
    public ?string $name = null;

    #[Assert\NotNull()]
    public BetCombinationItem $item1;

    #[Assert\NotNull()]
    public BetCombinationItem $item2;

    #[Assert\Positive]
    public string $stake = '0.00';
    public ?string $odd = null;

    public BetCombinationMethodEnum $method = BetCombinationMethodEnum::ONE_OR_TWO;

    public function __construct()
    {
        $this->item1 = new BetCombinationItem();
        $this->item2 = new BetCombinationItem();
    }

    public function isComplete(): bool
    {
        return null !== $this->odd
            && '0.00' !== $this->item1->stake
            && '0.00' !== $this->item2->stake;
    }
}

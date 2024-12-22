<?php

namespace App\Model\BettingSlip;

use App\Entity\BettingSlip;
use App\Utils\MathUtils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class BankrollPeriod
{
    public \DateTime $date;
    /** @var Collection<int, BettingSlip> */
    public Collection $bettingSlips;

    public string $balance = '0.00';

    public function __construct(\DateTime $date)
    {
        $this->date = $date;
        $this->bettingSlips = new ArrayCollection();
    }

    public function addBettingSlip(BettingSlip $bettingSlip): void
    {
        if ($this->bettingSlips->contains($bettingSlip)) {
            return;
        }

        $this->bettingSlips->add($bettingSlip);
        $this->computeBalance();
    }

    public function computeBalance(): void
    {
        $balance = '0.00';
        foreach ($this->bettingSlips as $bettingSlip) {
            $balance = MathUtils::add($balance, $bettingSlip->profit);
        }

        $this->balance = $balance;
    }
}

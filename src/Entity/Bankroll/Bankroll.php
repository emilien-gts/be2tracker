<?php

namespace App\Entity\Bankroll;

use App\Entity\Trait\IdTrait;
use App\Repository\Bankroll\BankrollRepository;
use App\Utils\MathUtils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BankrollRepository::class)]
#[UniqueEntity(fields: ['name'])]
class Bankroll
{
    use IdTrait;

    #[ORM\Column(length: 50, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    public ?string $name = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 3)]
    #[Assert\Positive(message: 'The value must be positive')]
    public string $capital = '0.00';

    /**
     * @var Collection<int, BettingSlip>
     */
    #[ORM\OneToMany(mappedBy: 'bankroll', targetEntity: BettingSlip::class, orphanRemoval: true)]
    public Collection $bettingSlips;

    public function __construct()
    {
        $this->bettingSlips = new ArrayCollection();
    }

    public function addBettingSlip(BettingSlip $bettingSlip): static
    {
        if (!$this->bettingSlips->contains($bettingSlip)) {
            $this->bettingSlips->add($bettingSlip);
            $bettingSlip->bankroll = $this;
        }

        return $this;
    }

    public function removeBettingSlip(BettingSlip $bettingSlip): static
    {
        if ($this->bettingSlips->removeElement($bettingSlip)) {
            if ($bettingSlip->bankroll === $this) {
                $bettingSlip->bankroll = null;
            }
        }

        return $this;
    }

    public function getBalance(): string
    {
        $balance = $this->capital;
        foreach ($this->bettingSlips as $bettingSlip) {
            $balance = MathUtils::add($balance, $bettingSlip->profit);
        }

        return $balance;
    }

    public function getProfit(): string
    {
        return MathUtils::sub($this->getBalance(), $this->capital);
    }

    public function getRoi(): string
    {
        if (MathUtils::eq($this->getTotalStake(), '0.00')) {
            return '0.00';
        }

        return MathUtils::mul(
            MathUtils::div($this->getProfit(), $this->getTotalStake()),
            '100'
        );
    }

    public function getTotalStake(): string
    {
        $totalStake = '0.00';
        foreach ($this->bettingSlips as $bettingSlip) {
            $totalStake = MathUtils::add($totalStake, $bettingSlip->stake);
        }

        return $totalStake;
    }

    public function __toString(): string
    {
        return (string) $this->name;
    }
}

<?php

namespace App\Entity;

use App\Model\Trait\IdTrait;
use App\Repository\BankrollRepository;
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

    #[ORM\Column(type: Types::DECIMAL, precision: 7, scale: 2)]
    #[Assert\PositiveOrZero]
    public ?string $startingBankroll = null;

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

    public function __toString(): string
    {
        return (string) $this->name;
    }
}

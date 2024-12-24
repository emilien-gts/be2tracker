<?php

namespace App\Entity;

use App\Enum\BettingSlipTypeEnum;
use App\Model\Trait\IdTrait;
use App\Repository\BettingSlipRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BettingSlipRepository::class)]
class BettingSlip
{
    use IdTrait;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull]
    public ?\DateTime $date = null;

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $name = null;

    #[ORM\Column(length: 255, enumType: BettingSlipTypeEnum::class)]
    #[Assert\NotBlank]
    public ?BettingSlipTypeEnum $type = null;

    #[ORM\ManyToOne(inversedBy: 'bettingSlips')]
    #[ORM\JoinColumn(nullable: false)]
    public ?Bankroll $bankroll = null;

    /**
     * @var Collection<int, Bet>
     */
    #[ORM\OneToMany(mappedBy: 'bettingSlip', targetEntity: Bet::class, cascade: ['ALL'], orphanRemoval: true)]
    public Collection $bets;

    public function __construct()
    {
        $this->bets = new ArrayCollection();
    }

    public function getBet(): ?Bet
    {
        if (BettingSlipTypeEnum::SIMPLE !== $this->type) {
            throw new \LogicException('Invalid betting slip type');
        }

        return $this->bets->first() ?: null;
    }

    public function setBet(Bet $bet): static
    {
        if (BettingSlipTypeEnum::SIMPLE !== $this->type) {
            throw new \LogicException('Invalid betting slip type');
        }

        $this->bets->clear();
        $this->bets->add($bet);
        $bet->bettingSlip = $this;

        return $this;
    }

    public function addBet(Bet $bet): static
    {
        if (!$this->bets->contains($bet)) {
            $this->bets->add($bet);
            $bet->bettingSlip = $this;
        }

        return $this;
    }

    public function removeBet(Bet $bet): static
    {
        if ($this->bets->removeElement($bet)) {
            // set the owning side to null (unless already changed)
            if ($bet->bettingSlip === $this) {
                $bet->bettingSlip = null;
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->name;
    }
}

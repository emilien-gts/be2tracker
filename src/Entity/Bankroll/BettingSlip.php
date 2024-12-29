<?php

namespace App\Entity\Bankroll;

use App\Entity\Trait\IdTrait;
use App\Enum\Bankroll\BetStatusEnum;
use App\Enum\Bankroll\BettingSlipTypeEnum;
use App\Repository\Bankroll\BettingSlipRepository;
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

    #[ORM\Column(length: 255, enumType: BetStatusEnum::class, options: ['default' => BetStatusEnum::PENDING])]
    public BetStatusEnum $status = BetStatusEnum::PENDING;

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 3)]
    #[Assert\Positive()]
    public string $odds = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 3)]
    #[Assert\Positive()]
    public string $stake = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 3)]
    public string $outcome = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 3)]
    public string $profit = '0.00';

    #[ORM\ManyToOne(inversedBy: 'bettingSlips')]
    #[ORM\JoinColumn(nullable: false)]
    public ?Bankroll $bankroll = null;

    /**
     * @var Collection<int, Bet>
     */
    #[ORM\OneToMany(mappedBy: 'bettingSlip', targetEntity: Bet::class, cascade: ['ALL'], orphanRemoval: true)]
    private Collection $bets;

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

    /**
     * @return Collection<int, Bet>
     */
    public function getBets(): Collection
    {
        if (BettingSlipTypeEnum::COMBINED !== $this->type) {
            throw new \LogicException('Invalid betting slip type');
        }

        return $this->bets;
    }

    public function addBet(Bet $bet): static
    {
        if (BettingSlipTypeEnum::COMBINED !== $this->type) {
            throw new \LogicException('Invalid betting slip type');
        }

        if (!$this->bets->contains($bet)) {
            $this->bets->add($bet);
            $bet->bettingSlip = $this;
        }

        return $this;
    }

    public function removeBet(Bet $bet): static
    {
        if (BettingSlipTypeEnum::COMBINED !== $this->type) {
            throw new \LogicException('Invalid betting slip type');
        }

        if ($this->bets->removeElement($bet)) {
            // set the owning side to null (unless already changed)
            if ($bet->bettingSlip === $this) {
                $bet->bettingSlip = null;
            }
        }

        return $this;
    }

    public function hasBet(BetStatusEnum ...$statuses): bool
    {
        return $this->bets->exists(fn (int $key, Bet $bet) => in_array($bet->status, $statuses));
    }

    public function hasOnlyBet(BetStatusEnum $status): bool
    {
        return $this->bets->forAll(fn (int $key, Bet $bet) => $bet->status === $status);
    }

    public function isSimple(): bool
    {
        return BettingSlipTypeEnum::SIMPLE === $this->type;
    }

    public function isWon(): bool
    {
        return BetStatusEnum::WON === $this->status;
    }

    public function __toString(): string
    {
        if (null !== $this->name) {
            return (string) $this->name;
        }

        if (BettingSlipTypeEnum::SIMPLE === $this->type) {
            return (string) $this->getBet()?->name;
        }

        return sprintf('Combined %d bets', $this->bets->count());
    }
}

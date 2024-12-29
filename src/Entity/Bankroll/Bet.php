<?php

namespace App\Entity\Bankroll;

use App\Entity\Trait\IdTrait;
use App\Enum\Bankroll\BetStatusEnum;
use App\Repository\Bankroll\BetRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BetRepository::class)]
class Bet
{
    use IdTrait;

    #[ORM\ManyToOne(inversedBy: 'bets')]
    #[ORM\JoinColumn(nullable: false)]
    public ?BettingSlip $bettingSlip = null;

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $name = null;

    #[ORM\Column(length: 255, enumType: BetStatusEnum::class, options: ['default' => BetStatusEnum::PENDING])]
    public BetStatusEnum $status = BetStatusEnum::PENDING;

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 3)]
    #[Assert\Positive]
    public string $odds = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 3, nullable: true)]
    #[Assert\Positive]
    #[Assert\When(
        expression: 'this.bettingSlip.type.value === "combined"',
        constraints: [new Assert\IsNull()]
    )]
    public ?string $stake = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 3, nullable: true)]
    #[Assert\When(
        expression: 'this.bettingSlip.type.value === "combined"',
        constraints: [new Assert\IsNull()]
    )]
    public ?string $outcome = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 3, nullable: true)]
    #[Assert\When(
        expression: 'this.bettingSlip.type.value === "combined"',
        constraints: [new Assert\IsNull()]
    )]
    public ?string $profit = null;

    public function isCancelled(): bool
    {
        return BetStatusEnum::CANCELLED === $this->status;
    }

    public function isRefunded(): bool
    {
        return BetStatusEnum::REFUNDED === $this->status;
    }

    public function __toString(): string
    {
        return $this->name ?? '';
    }
}

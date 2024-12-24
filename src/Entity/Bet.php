<?php

namespace App\Entity;

use App\Enum\BetStatusEnum;
use App\Model\Trait\IdTrait;
use App\Repository\BetRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BetRepository::class)]
class Bet
{
    use IdTrait;

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $name = null;

    #[ORM\Column(length: 255, enumType: BetStatusEnum::class, options: ['default' => BetStatusEnum::PENDING])]
    public BetStatusEnum $status = BetStatusEnum::PENDING;

    #[ORM\Column(type: Types::DECIMAL, precision: 7, scale: 2)]
    public ?string $odds = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 7, scale: 2)]
    public ?string $stake = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 7, scale: 2)]
    public ?string $outcome = null;

    #[ORM\ManyToOne(inversedBy: 'bets')]
    #[ORM\JoinColumn(nullable: false)]
    public ?BettingSlip $bettingSlip = null;
}

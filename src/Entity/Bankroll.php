<?php

namespace App\Entity;

use App\Model\IdTrait;
use App\Repository\BankrollRepository;
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
    private ?string $name = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 7, scale: 2)]
    #[Assert\Positive]
    private ?string $startingBankroll = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getStartingBankroll(): ?string
    {
        return $this->startingBankroll;
    }

    public function setStartingBankroll(string $startingBankroll): static
    {
        $this->startingBankroll = $startingBankroll;

        return $this;
    }
}

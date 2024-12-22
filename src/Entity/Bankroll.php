<?php

namespace App\Entity;

use App\Model\IdTrait;
use App\Repository\BankrollRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BankrollRepository::class)]
class Bankroll
{
    use IdTrait;
}

<?php

namespace App\Service;

use App\Entity\Bankroll;
use Doctrine\ORM\EntityManagerInterface;

class BankrollService
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function create(): Bankroll
    {
        return new Bankroll();
    }

    public function save(Bankroll $bankroll): void
    {
        $this->em->persist($bankroll);
        $this->em->flush();
    }
}

<?php

namespace App\Repository;

use App\Entity\Bankroll;
use App\Entity\BettingSlip;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BettingSlip>
 */
class BettingSlipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BettingSlip::class);
    }

    /**
     * @return array<BettingSlip>
     */
    public function search(Bankroll $bankroll, array $options = []): array
    {
        return $this->searchQb($bankroll, $options)
            ->getQuery()
            ->getResult();
    }

    public function searchQb(Bankroll $bankroll, array $options = []): QueryBuilder
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('e', 'bet');
        $qb->innerJoin('e.bets', 'bet');

        $qb->andWhere('e.bankroll = :bankroll');
        $qb->setParameter('bankroll', $bankroll);

        if (isset($options['order_by'])) {
            $qb->orderBy(\sprintf('e.%s', $options['order_by']), $options['order']);
        }

        return $qb;
    }
}

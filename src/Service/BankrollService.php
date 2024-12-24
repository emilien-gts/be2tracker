<?php

namespace App\Service;

use App\Entity\Bankroll;
use App\Entity\BettingSlip;
use App\Repository\BettingSlipRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class BankrollService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly BettingSlipRepository $repository,
        private readonly ChartBuilderInterface $chartBuilder,
    ) {
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

    public function buildChart(Bankroll $bankroll): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);
        $items = $this->repository->search($bankroll, [
            'order_by' => 'date',
            'order' => 'ASC',
        ]);

        $chart->setData([
            'labels' => \array_map(fn (BettingSlip $bs) => $bs->date?->format('d/m/Y'), $items),
            'datasets' => [
                [
                    'backgroundColor' => 'rgba(255, 99, 132, 0.5)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => \array_map(fn (BettingSlip $bs) => $bs->getBet()?->odds, $items),
                    'fill' => true,
                ],
            ],
        ]);

        $chart->setOptions([
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'maintainAspectRatio' => false,
            'scales' => [
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                ],
                'y' => [
                    'grid' => [
                        'display' => false,
                    ],
                ],
            ],
        ]);

        return $chart;
    }
}

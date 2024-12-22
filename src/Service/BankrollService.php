<?php

namespace App\Service;

use App\Entity\Bankroll;
use App\Model\Bankroll\BankrollMovement;
use App\Model\BettingSlip\BankrollPeriod;
use App\Repository\BettingSlipRepository;
use App\Utils\MathUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class BankrollService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly BettingSlipRepository $bettingSlipRepository,
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

    public function delete(Bankroll $bankroll): void
    {
        $this->em->remove($bankroll);
        $this->em->flush();
    }

    public function buildChart(Bankroll $bankroll): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);
        $items = $this->computeChartData($bankroll);

        $chart->setData([
            'labels' => \array_map(fn (BankrollMovement $bm) => $bm->date->format('d/m/Y'), $items),
            'datasets' => [
                [
                    'backgroundColor' => 'rgba(255, 99, 132, 0.5)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => \array_map(fn (BankrollMovement $bm) => $bm->currentBalance, $items),
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
                    'ticks' => [
                        'display' => false,
                    ],
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

    /**
     * @return BankrollMovement[]
     */
    private function computeChartData(Bankroll $bankroll): array
    {
        $bss = $this->bettingSlipRepository->search($bankroll, [
            'order_by' => 'date',
            'order' => 'ASC',
        ]);

        $balance = $bankroll->capital;
        $dataset = [];

        foreach ($bss as $bs) {
            if (null === $bs->date) {
                continue;
            }

            $balance = MathUtils::add($balance, $bs->profit);
            $dataset[] = new BankrollMovement($bs->date, $bs->profit, $balance);
        }

        return $dataset;
    }

    public function getBankrollPeriods(Bankroll $bankroll): array
    {
        $periods = [];

        $bss = $this->bettingSlipRepository->search($bankroll, [
            'order_by' => 'date',
            'order' => 'DESC',
        ]);

        foreach ($bss as $bs) {
            if (null === $bs->date) {
                continue;
            }

            $mY = (clone $bs->date)->modify('first day of this month');
            $key = $mY->format('mY');

            if (!isset($periods[$key])) {
                $period = new BankrollPeriod($mY);
                $period->addBettingSlip($bs);
                $periods[$key] = $period;
            } else {
                $periods[$key]->addBettingSlip($bs);
            }
        }

        return $periods;
    }
}

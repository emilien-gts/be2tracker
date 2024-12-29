<?php

namespace App\Import;

use App\Entity\Bankroll\Bankroll;
use App\Entity\Bankroll\Bet;
use App\Entity\Bankroll\BettingSlip;
use App\Enum\Bankroll\BetStatusEnum;
use App\Enum\Bankroll\BettingSlipTypeEnum;
use App\Service\Bankroll\BankrollService;
use App\Service\Bankroll\BettingSlip\BettingSlipEditorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class ImportBankrollService
{
    /** @var array<string, BettingSlip> */
    private array $combined = [];

    public function __construct(
        #[Autowire('%kernel.project_dir%/src/Import/Resources/dataset.csv')] private readonly string $filePath,
        private readonly EntityManagerInterface $em,
        private readonly BankrollService $bankrollService,
        private readonly BettingSlipEditorService $bsEditorService,
    ) {
    }

    public function import(string $name, string $capital): void
    {
        $bankroll = $this->em->getRepository(Bankroll::class)->findOneBy(['name' => $name]);
        if (null !== $bankroll) {
            throw new \InvalidArgumentException('Bankroll already exists');
        }

        $bankroll = $this->bankrollService->create();
        $bankroll->name = $name;
        $bankroll->capital = $capital;
        $this->em->persist($bankroll);

        $this->importRows($bankroll);
    }

    private function importRows(Bankroll $bankroll): void
    {
        $line = 0;
        $handle = \fopen($this->filePath, 'r');
        if (false === $handle) {
            throw new \RuntimeException('Cannot open file');
        }

        while (($row = \fgetcsv($handle)) !== false) {
            ++$line;
            if (1 === $line) { // header
                continue;
            }

            $this->importBettingSlip($row, $bankroll);
        }

        \fclose($handle);
    }

    private function importBettingSlip(array $row, Bankroll $bankroll): void
    {
        $type = $this->getType($row[9]);
        match ($type) {
            BettingSlipTypeEnum::SIMPLE => $this->importSimpleBs($row, $bankroll),
            BettingSlipTypeEnum::COMBINED => $this->importCombinedBs($row, $bankroll),
            null => $this->addBetToCombinedBs($row),
        };
    }

    private function importSimpleBs(array $row, Bankroll $bankroll): void
    {
        $bs = new BettingSlip();
        $bs->type = BettingSlipTypeEnum::SIMPLE;
        $bs->date = new \DateTime($row[0]);
        $bs->bankroll = $bankroll;

        $bet = new Bet();
        $bet->name = $row[10];
        $bet->odds = $row[11];
        $bet->stake = $row[12];
        $bet->status = $this->getStatus($row[17]);
        $bs->setBet($bet);

        $this->bsEditorService->getEditor($bs)->save($bs);
    }

    private function importCombinedBs(array $row, Bankroll $bankroll): void
    {
        $bs = new BettingSlip();
        $bs->type = BettingSlipTypeEnum::COMBINED;
        $bs->date = new \DateTime($row[0]);
        $bs->bankroll = $bankroll;
        $bs->stake = $row[12];
        $bs->name = $row[10];

        $this->bsEditorService->getEditor($bs)->save($bs);
        $this->combined[$bs->getIdString()] = $bs;
    }

    private function addBetToCombinedBs(array $row): void
    {
        /** @var BettingSlip $bs */
        $bs = end($this->combined);

        $bet = new Bet();
        $bet->name = $row[10];
        $bet->odds = $row[11];
        $bet->status = $this->getStatus($row[17]);

        $bs->addBet($bet);
        $this->bsEditorService->getEditor($bs)->save($bs);
    }

    private function getType(string $type): ?BettingSlipTypeEnum
    {
        return match ($type) {
            'Simple' => BettingSlipTypeEnum::SIMPLE,
            'Combined' => BettingSlipTypeEnum::COMBINED,
            default => null,
        };
    }

    private function getStatus(string $status): BetStatusEnum
    {
        return match ($status) {
            'Won' => BetStatusEnum::WON,
            'Lost' => BetStatusEnum::LOST,
            default => throw new \InvalidArgumentException('Invalid status'),
        };
    }
}

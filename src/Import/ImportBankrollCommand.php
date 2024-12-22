<?php

namespace App\Import;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'import:bankroll',
    description: 'Import bankroll from bet analytics',
)]
class ImportBankrollCommand extends Command
{
    public function __construct(private readonly ImportBankrollService $service)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');
        $question = new Question('Please enter the name of the bankroll : ', 'My Bankroll');
        $name = $helper->ask($input, $output, $question);

        $question = new Question('Please enter the capital of the bankroll (numeric value) : ', '1000');
        $capital = $helper->ask($input, $output, $question);

        try {
            $this->service->import($name, $capital);
        } catch (\Exception $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        $io->success('Bankroll imported successfully!');

        return Command::SUCCESS;
    }
}

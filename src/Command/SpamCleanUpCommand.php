<?php

namespace App\Command;

use App\Repository\eReportsRepository;
use App\Repository\eSuggestionsRepository;
use App\Repository\FeedbackRepository;
use App\Repository\HistoryRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

//https://symfony.com/doc/5.4/the-fast-track/en/24-cron.html#setting-up-a-cron-on-platform-sh

class SpamCleanUpCommand extends Command
{
    private $reportsRepository;
    private $feedbackRepository;
    private $historyRepository;
    private $suggestionsRepository;

    protected static $defaultName = 'app:trash:cleanup';

    public function __construct(eReportsRepository $reportsRepository, FeedbackRepository $feedbackRepository, HistoryRepository $historyRepository,  eSuggestionsRepository $suggestionsRepository)
    {
        $this->reportsRepository = $reportsRepository;
        $this->feedbackRepository = $feedbackRepository;
        $this->historyRepository = $historyRepository;
        $this->suggestionsRepository = $suggestionsRepository;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Deletes all reports and feedback declared as SPAM, created 2 days ago, from the database')
            ->setDescription('Deletes all history records created a month ago')
            ->setDescription('Deletes all suggestions already decided 3 days ago')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Dry run')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($input->getOption('dry-run')) {
            $io->note('Dry mode enabled');

            $countR = $this->reportsRepository->countSpam();
            $countF = $this->feedbackRepository->countSpam();
            $countH = $this->historyRepository->countOld();
            $countS = $this->suggestionsRepository->countDue();

        } else {
            $countR = $this->reportsRepository->deleteSpam();
            $countF = $this->feedbackRepository->deleteSpam();
            $countH = $this->historyRepository->deleteOld();
            $countS = $this->suggestionsRepository->deleteDue();
        }

        $io->success(sprintf('Deleted "%d" spam reports.', $countR));
        $io->success(sprintf('Deleted "%d" spam feedback.', $countF));
        $io->success(sprintf('Deleted "%d" old history records.', $countH));
        $io->success(sprintf('Deleted "%d" decided suggestions.', $countS));

        return 0;
    }
}
<?php

namespace App\Command;

use App\Repository\eReportsRepository;
use App\Repository\eSuggestionsRepository;
use App\Repository\FeedbackRepository;
use App\Repository\HistoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

//https://symfony.com/doc/5.4/the-fast-track/en/24-cron.html#setting-up-a-cron-on-platform-sh

class DatabaseTruncateCommand extends Command
{

    protected static $defaultName = 'app:database:truncate';
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Clears up the database except sources and users')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $array = ["Certification", "Comment", "eAttempt", "eProvider", "eReport", "eStars", "Exam", "ExamPaper", "History", "Proposition", "Question"];

        $io = new SymfonyStyle($input, $output);

        $connection = $this->entityManager->getConnection();
        $connection->beginTransaction();
        
        try {
            $connection->executeStatement('SET FOREIGN_KEY_CHECKS=0');

            foreach ($array as $className) {
                $rows = $connection->executeStatement('DELETE FROM '.$this->entityManager->getClassMetadata('App\\Entity\\'.$className)->getTableName());
                $io->success("Truncated table ".$className." (".$rows." rows were deleted)");
            }

            $connection->executeStatement('SET FOREIGN_KEY_CHECKS=1');

            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollback();
            $io->error($e->getMessage());
        }

        // $countR = $this->reportsRepository->deleteSpam();
        // $io->success(sprintf('Deleted "%d" spam reports.', $countR));

        return 0;
    }
}
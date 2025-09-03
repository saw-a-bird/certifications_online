<?php

namespace App\Command;

use App\Services\PDFtoHTMLService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

//https://symfony.com/doc/5.4/the-fast-track/en/24-cron.html#setting-up-a-cron-on-platform-sh

class TestCommand extends Command
{
    protected static $defaultName = 'app:test';
    private $variable;
    public function __construct(PDFtoHTMLService $pdfService)
    {
        $this->variable = $pdfService;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Testing stuff.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->variable->extract($this->io);

        return 0;
    }
}
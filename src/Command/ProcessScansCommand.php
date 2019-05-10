<?php

namespace App\Command;

use App\Entity\Scan;
use App\Service\ScanProcessor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ProcessScansCommand extends Command
{
    protected static $defaultName = 'app:processScans';

    protected function configure()
    {
        $this
            ->setDescription('process pending scan requests')
            ->addArgument('max', InputArgument::OPTIONAL, 'Maximum Number of unprocessed Scans to process (default 1)');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io  = new SymfonyStyle($input, $output);
        $max = $input->getArgument('max');

        if (!$max) {
            $max = 1;
        }

        $sp = new ScanProcessor(null);

        $scansToProcess = $sp->getScans($max);
        foreach ($scansToProcess as $scan) {
            try {
                $sp->scanDir($scan);
            } catch (\Exception $e) {

            }
        }

    }
}

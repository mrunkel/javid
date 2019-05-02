<?php

namespace App\Command;

use App\Entity\Scan;
#use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ProcessScansCommand extends Command
{
    protected static $defaultName = 'app:processScans';
    private $container;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct();
        $this->container = $container;
    }

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


        $scans   = $this->container->get('doctrine')->getRepository(Scan::class);
        $mgr     = $this->container->get('doctrine')->getManager();
        $process = $scans->findBy(['completed' => 0], $max);
        foreach ($process as $scan) {
            $scan->scanDir();
        }

    }
}

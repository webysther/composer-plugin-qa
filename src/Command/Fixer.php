<?php

namespace Webs\QA\Command;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Style\SymfonyStyle;

class Fixer extends BaseCommand
{
    protected $description = 'Run qa:code-beautifier-fixer and qa:php-cs-fixer';

    protected function configure()
    {
        $this->setName('qa:fixer')
            ->setDescription($this->description)
            ->addArgument(
                'source',
                InputArgument::IS_ARRAY|InputArgument::OPTIONAL,
                'List of directories to search  Default:src,app,tests'
            )
            ->addOption(
                'standard',
                null,
                InputOption::VALUE_REQUIRED,
                'List of standards',
                'PSR1,PSR2'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $start = microtime(true);
        $commands = array('qa:cbf', 'qa:csf');
        $io = new SymfonyStyle($input, $output);
        $output->write(sprintf("\033\143"));

        foreach ($commands as $command) {
            $returnCode = $this->getApplication()->find($command)->run($input, $output);
            if ($returnCode) {
                $output->writeln('<error>Exit code ' . $returnCode . '</>');
            }
        }

        $end = microtime(true);
        $time = round($end-$start);
        $io->newLine();
        $io->section("Results");
        $output->writeln('<info>Time: ' . $time . ' seconds</>');
    }
}

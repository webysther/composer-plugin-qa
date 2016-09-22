<?php

namespace Webs\QA\Command;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class SecurityChecker extends BaseCommand
{
    protected $description = 'SecurityChecker';

    protected function configure()
    {
        $this->setName('qa:security-checker')->setDescription($this->description);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $start = microtime(true);
        $io = new SymfonyStyle($input, $output);
        $io->title($this->description);

        $util = new Util();
        $sec = $util->checkBinary('security-checker');
        $output->writeln($util->checkVersion($sec));

        $cmd = $sec . ' --ansi security:check';
        $output->writeln('<info>Command: ' . $cmd . '</>');
        $io->newLine();
        $process = new Process($cmd);
        $process->run();
        $output->writeln($process->getOutput());
        $end = microtime(true);
        $time = round($end-$start);

        $io->section("Results");
        $output->writeln('<info>Time: ' . $time . ' seconds</>');
        $io->newLine();
        return $process->getExitCode();
    }
}

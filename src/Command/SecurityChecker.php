<?php

namespace Webs\QA\Command;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class SecurityChecker extends BaseCommand
{
    protected $description = 'SecurityChecker';

    protected function configure()
    {
        $this->setName('qa:security-checker')
            ->setDescription($this->description);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $start = microtime(true);
        $output->writeln('<comment>Running ' . $this->description . '...</comment>');

        $sc = 'vendor/bin/security-checker';
        if(!file_exists($sc)){
            $process = new Process('security-checker --help');
            $process->run();
            if ($process->isSuccessful()) {
                $sc = 'security-checker';
            } else {
                throw new ProcessFailedException($process);
            }
        }

        $cmd = $sc . ' --ansi security:check';
        $process = new Process($cmd);
        $process->run();
        $output->writeln($process->getOutput());
        $end = microtime(true);
        $time = round($end-$start);

        $output->writeln('<comment>Command executed `' . $cmd . '` in ' . $time . ' seconds</comment>');
        exit($process->getExitCode());
    }
}

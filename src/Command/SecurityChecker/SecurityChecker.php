<?php

namespace Webs\QA\Command\SecurityChecker;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;
use Webs\QA\Command\Util;

/**
 * Checks if your application uses dependencies with known security vulnerabilities.
 */
class SecurityChecker extends BaseCommand
{
    /**
     * Console description.
     *
     * @var string
     */
    protected $description = 'SecurityChecker';

    /**
     * Console params configuration.
     */
    protected function configure()
    {
        $this->setName('qa:security-checker')->setDescription($this->description);
    }

    /**
     * Execution.
     *
     * @param InputInterface  $input  Input console
     * @param OutputInterface $output Output console
     *
     * @return int Exit code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $start = microtime(true);
        $style = new SymfonyStyle($input, $output);
        $style->setDecorated(true);
        $style->title($this->description);

        $util = new Util();
        $sec = $util->checkBinary('security-checker');
        $output->writeln($util->checkVersion($sec));

        $cmd = $sec.' --ansi security:check';
        $output->writeln('<info>Command: '.$cmd.'</>');
        $style->newLine();
        $process = new Process($cmd);
        $process->run();
        $output->writeln($process->getOutput());
        $end = microtime(true);
        $time = round($end - $start);

        $style->section('Results');
        $output->writeln('<info>Time: '.$time.' seconds</>');
        $style->newLine();

        return $process->getExitCode();
    }
}

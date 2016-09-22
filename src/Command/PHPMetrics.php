<?php

namespace Webs\QA\Command;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class PHPMetrics extends BaseCommand
{
    protected $description = 'PHP Metrics';

    protected function configure()
    {
        $this->setName('qa:php-metrics')
            ->setDescription($this->description)
            ->addArgument(
                'source',
                InputArgument::IS_ARRAY|InputArgument::OPTIONAL,
                'List of directories to search <comment>[Default:"src,app,tests"]</>'
            )
            ->addOption(
                'diff',
                null,
                InputOption::VALUE_NONE,
                'Use `git status -s` to search files to check'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $start = microtime(true);
        $this->output = $output;
        $command = $this;
        $io = new SymfonyStyle($input, $output);
        $io->title($this->description);

        $util = new Util();
        $pm = $util->checkBinary('phpmetrics');
        $output->writeln($util->checkVersion($pm));
        $source = $util->checkSource($input);
        if ($input->getOption('diff')) {
            $source = $util->getDiffSource();
        }

        $sources = explode(' ', $source);
        $options = ' --report-cli --ansi --without-oop --ignore-errors --level=4 --excluded-dirs=\'.git\' ';
        $exitCode = 0;
        foreach ($sources as $source) {
            $cmd = $pm . $options . $source;
            $output->writeln('<info>Command: ' . $cmd . '</>');
            $io->newLine();
            $process = new Process($cmd);
            $process->setTimeout(3600)->run(function ($type, $buffer) use ($command) {
                if (strpos($buffer, ']') !== false) {
                    return;
                }
                $command->output->write($buffer);
            });

            if (!$exitCode) {
                $exitCode = $process->getExitCode();
            }
        }

        $end = microtime(true);
        $time = round($end-$start);

        $io->section("Results");
        $output->writeln('<info>Time: ' . $time . ' seconds</>');
        $io->newLine();
        return $exitCode;
    }
}

<?php

/**
 * Composer Plugin QA
 *
 * @author Webysther Nunes <webysther@gmail.com>
 */

namespace Webs\QA\Command\PHPMetrics;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;
use Webs\QA\Command\Util;

/**
 * Static analysis tool, gives metrics about PHP project and classes
 */
class PHPMetrics extends BaseCommand
{
    /**
     * Console description
     *
     * @var string
     */
    protected $description = 'PHP Metrics';

    /**
     * Console params configuration
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('qa:php-metrics')
            ->setDescription($this->description)
            ->addArgument(
                'source',
                InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
                'List of directories to search <comment>[Default:"src,app,tests"]</>'
            )
            ->addOption(
                'diff',
                null,
                InputOption::VALUE_NONE,
                'Use `git status -s` to search files to check'
            );
    }

    /**
     * Execution
     *
     * @param  InputInterface  $input  Input console
     * @param  OutputInterface $output Output console
     * @return integer                 Exit code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $start = microtime(true);
        $this->output = $output;
        $command = $this;
        $style = new SymfonyStyle($input, $output);
        $style->title($this->description);

        $util = new Util();
        $phpmetrics = $util->checkBinary('phpmetrics');
        $output->writeln($util->checkVersion($phpmetrics));
        $source = $util->checkSource($input);
        if ($input->getOption('diff')) {
            $source = $util->getDiffSource();
        }

        $sources = explode(' ', $source);
        $options = ' --report-cli --ansi --without-oop --ignore-errors --level=4 --excluded-dirs=\'.git\' ';
        $exitCode = 0;
        foreach ($sources as $source) {
            $cmd = $phpmetrics.$options.$source;
            $output->writeln('<info>Command: '.$cmd.'</>');
            $style->newLine();
            $process = new Process($cmd);
            $process->setTimeout(3600)->run(function ($type, $buffer) use ($command) {
                if (strpos($buffer, ']') !== false || Process::ERR == $type) {
                    return;
                }
                $command->output->write($buffer);
            });

            if (!$exitCode) {
                $exitCode = $process->getExitCode();
            }
        }

        $end = microtime(true);
        $time = round($end - $start);

        $style->section('Results');
        $output->writeln('<info>Time: '.$time.' seconds</>');
        $style->newLine();

        return $exitCode;
    }
}

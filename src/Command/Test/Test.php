<?php

/**
 * Composer Plugin QA.
 *
 * @author Webysther Nunes <webysther@gmail.com>
 */

namespace Webs\QA\Command\Test;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;
use Webs\QA\Command\Util;

/**
 * Testing Framework.
 */
class Test extends BaseCommand
{
    /**
     * Console description.
     *
     * @var string
     */
    protected $description = 'PHPUnit';

    /**
     * Console params configuration.
     */
    protected function configure()
    {
        $this->setName('qa:test')
            ->addArgument(
                'source',
                InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
                'List of directories/files to search'
            )
            ->addOption(
                'stop-on-failure',
                null,
                InputOption::VALUE_NONE,
                'Stop in case of failure'
            )
            ->addOption(
                'diff',
                null,
                InputOption::VALUE_NONE,
                'Use `git status -s` to search files to check. <comment>Use only the first occurrency.</>'
            )
            ->setDescription($this->description);
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
        $this->output = $output;
        $command = $this;
        $style = new SymfonyStyle($input, $output);
        $style->title($this->description);

        $util = new Util();
        $test = $util->checkBinary('phpunit');

        $source = '';
        if ($input->getArgument('source')) {
            $source = ' '.$util->checkSource($input);
        }

        if ($input->getOption('diff')) {
            $sources = explode(' ', $util->getDiffSource());
            foreach ($sources as $file) {
                if (strpos($file, 'tests/') !== false) {
                    $source = ' '.$file;
                    break; // Use only the first occurrency
                }
            }
        }

        $stopFail = '';
        if ($input->getOption('stop-on-failure')) {
            $stopFail = ' --stop-on-failure';
        }

        $cmd = $test.$source.' --report-useless-tests --colors=always'.$stopFail;
        $output->writeln('<info>Command: '.$cmd.'</>');
        $style->newLine();
        $process = new Process($cmd);
        $process->setTimeout(3600)->run(function ($type, $buffer) use ($command) {
            if (Process::ERR == $type) {
                return;
            }
            $command->output->write($buffer);
        });
        $end = microtime(true);
        $time = round($end - $start);

        $style->newLine();
        $style->section('Results');
        $output->writeln('<info>Time: '.$time.' seconds</>');
        $style->newLine();

        return $process->getExitCode();
    }
}

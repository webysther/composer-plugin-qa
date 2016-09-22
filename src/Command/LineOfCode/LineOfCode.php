<?php

/**
 * Composer Plugin QA
 *
 * @author Webysther Nunes <webysther@gmail.com>
 */

namespace Webs\QA\Command\LineOfCode;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;
use Webs\QA\Command\Util;

/**
 * A tool for quickly measuring the size of a PHP project
 */
class LineOfCode extends BaseCommand
{
    /**
     * Console description
     *
     * @var string
     */
    protected $description = 'Line of Code';

    /**
     * Console params configuration
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('qa:line-of-code')
            ->setDescription($this->description)
            ->addArgument(
                'source',
                InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
                'List of directories/files to search <comment>[Default:"src,app,tests"]</>'
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
        $style = new SymfonyStyle($input, $output);
        $style->title($this->description);

        $util = new Util();
        $loc = $util->checkBinary('phploc');
        $source = $util->checkSource($input);
        if ($input->getOption('diff')) {
            $source = $util->getDiffSource();
        }

        $cmd = $loc.' '.$source.' --ansi --count-tests';
        $process = new Process($cmd);
        $process->run();
        $output->writeln($process->getOutput());
        $end = microtime(true);
        $time = round($end - $start);

        $style->section('Results');
        $output->writeln('<info>Command: '.$cmd.'</>');
        $output->writeln('<info>Time: '.$time.' seconds</>');
        $style->newLine();

        return $process->getExitCode();
    }
}

<?php

namespace Webs\QA\Command\MessDetector;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;
use Webs\QA\Command\Util;

/**
 * User friendly frontend application for the raw metrics stream measured by PHP Depend.
 */
class MessDetector extends BaseCommand
{
    /**
     * Console description.
     *
     * @var string
     */
    protected $description = 'Mess Detector';

    /**
     * Console params configuration.
     */
    protected function configure()
    {
        $this->setName('qa:mess-detector')
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
        $phpmd = $util->checkBinary('phpmd');
        $output->writeln($util->checkVersion($phpmd));
        $source = $util->checkSource($input, ',');
        if ($input->getOption('diff')) {
            $source = $util->getDiffSource(',');
        }

        $cmd = $phpmd.' '.$source.' text phpmd.xml';
        $output->writeln('<info>Command: '.$cmd.'</>');
        $style->newLine();
        $process = new Process($cmd);
        $exitCode = $process->setTimeout(3600)->run();
        $output->writeln($this->format($process->getOutput()));
        $end = microtime(true);
        $time = round($end - $start);

        $style->section('Results');
        $output->writeln('<info>Time: '.$time.' seconds</>');
        $style->newLine();

        return $exitCode;
    }

    /**
     * @todo  Make PR for this
     */
    protected function format($output)
    {
        $output = str_replace(PHP_EOL, " \033[0m ".PHP_EOL, $output);
        $output = str_replace(realpath(__DIR__.'/..').'/', '', $output);
        $output = str_replace("\t", " \033[1;31m ".PHP_EOL, $output);

        return str_replace('. ', '.'.PHP_EOL, $output);
    }
}

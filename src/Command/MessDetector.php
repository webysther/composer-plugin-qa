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

class MessDetector extends BaseCommand
{
    protected $description = 'Mess Detector';

    protected function configure()
    {
        $this->setName('qa:mess-detector')
            ->setDescription($this->description)
            ->addArgument(
                'source',
                InputArgument::IS_ARRAY|InputArgument::OPTIONAL,
                'List of directories/files to search <comment>[Default:"src,app,tests"]</>'
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
        $io = new SymfonyStyle($input, $output);
        $io->title($this->description);

        $util = new Util();
        $md = $util->checkBinary('phpmd');
        $output->writeln($util->checkVersion($md));
        $source = $util->checkSource($input, ',');
        if ($input->getOption('diff')) {
            $source = $util->getDiffSource(',');
        }

        $cmd = $md . ' ' . $source . ' text phpmd.xml';
        $output->writeln('<info>Command: ' . $cmd . '</>');
        $io->newLine();
        $process = new Process($cmd);
        $exitCode = $process->setTimeout(3600)->run();
        $output->writeln($this->format($process->getOutput()));
        $end = microtime(true);
        $time = round($end-$start);

        $io->section("Results");
        $output->writeln('<info>Time: ' . $time . ' seconds</>');
        $io->newLine();
        return $exitCode;
    }

    /**
     * @todo  Make PR for this
     */
    protected function format($output)
    {
        $output = str_replace(PHP_EOL, " \033[0m ".PHP_EOL, $output);
        $output = str_replace(realpath(__DIR__."/..")."/", '', $output);
        $output = str_replace("\t", " \033[1;31m " . PHP_EOL, $output);
        return str_replace(". ", ".".PHP_EOL, $output);
    }
}

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

class CodeSniffer extends BaseCommand
{
    protected $description = 'Code Sniffer';

    protected function configure()
    {
        $this->setName('qa:code-sniffer')
            ->setDescription($this->description)
            ->addArgument(
                'source',
                InputArgument::IS_ARRAY|InputArgument::OPTIONAL,
                'List of directories/files to search  <comment>[Default:"src,app,tests"]</>'
            )
            ->addOption(
                'standard',
                null,
                InputOption::VALUE_REQUIRED,
                'List of standards  Default:PSR1,PSR2',
                'PSR1,PSR2'
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
        $io = new SymfonyStyle($input, $output);
        $io->title($this->description);

        $util = new Util();
        $cs = $util->checkBinary('phpcs');
        $output->writeln($util->checkVersion($cs));
        $standard = $input->getOption('standard');
        $source = $util->checkSource($input);

        if ($input->getOption('diff')) {
            $source = $util->getDiffSource();
        }

        if (empty($source)) {
            $output->writeln('<error>No files found</>');
            $io->newLine();
            return 1;
        }

        $cmd = $cs . ' ' . $source . ' --colors --standard='.$standard;
        $process = new Process($cmd);
        $command = $this;
        $process->run(function ($type, $buffer) use ($command) {
            $command->output->writeln($buffer);
        });
        $end = microtime(true);
        $time = round($end-$start);

        $io->section("Results");
        $output->writeln('<info>Command: ' . $cmd . '</>');
        $output->writeln('<info>Time: ' . $time . ' seconds</>');
        $io->newLine();
        return $process->getExitCode();
    }
}

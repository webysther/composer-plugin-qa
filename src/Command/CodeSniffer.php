<?php

/**
 * Composer Plugin QA
 *
 * @author Webysther Nunes <webysther@gmail.com>
 */

namespace Webs\QA\Command;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

/**
 * Detects violations of a defined set of coding standards
 */
class CodeSniffer extends BaseCommand
{
    protected $description = 'Code Sniffer';

    protected function configure()
    {
        $this->setName('qa:code-sniffer')
            ->setDescription($this->description)
            ->addArgument(
                'source',
                InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
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
        $style = new SymfonyStyle($input, $output);
        $style->title($this->description);

        $util = new Util();
        $phpcs = $util->checkBinary('phpcs');
        $output->writeln($util->checkVersion($phpcs));
        $standard = $input->getOption('standard');
        $source = $util->checkSource($input);

        if ($input->getOption('diff')) {
            $source = $util->getDiffSource();
        }

        if (empty($source)) {
            $output->writeln('<error>No files found</>');
            $style->newLine();

            return 1;
        }

        $cmd = $phpcs.' '.$source.' --colors --standard='.$standard;
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

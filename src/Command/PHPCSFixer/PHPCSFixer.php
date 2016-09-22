<?php

/**
 * Composer Plugin QA.
 *
 * @author Webysther Nunes <webysther@gmail.com>
 */

namespace Webs\QA\Command\PHPCSFixer;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Style\SymfonyStyle;
use Webs\QA\Command\Util;

/**
 * A tool to automatically fix coding standards issues.
 */
class PHPCSFixer extends BaseCommand
{
    /**
     * Console description.
     *
     * @var string
     */
    protected $description = 'PHP Code Sniffer Fixer';

    /**
     * Console params configuration.
     */
    protected function configure()
    {
        $this->setName('qa:php-cs-fixer')
            ->setDescription($this->description)
            ->addArgument(
                'source',
                InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
                'List of directories to search <comment>[Default:"src,app,tests"]</>'
            )
            ->addOption(
                'standard',
                null,
                InputOption::VALUE_REQUIRED,
                'List of standards',
                'PSR0,PSR1,PSR2,Symfony'
            )
            ->addOption(
                'diff',
                null,
                InputOption::VALUE_NONE,
                'Use `git status -s` to search files to check'
            )
            ->addOption(
                'fix',
                null,
                InputOption::VALUE_NONE,
                'Apply fix'
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
        $style->title($this->description);

        $util = new Util();
        $csf = $util->checkBinary('php-cs-fixer');
        $output->writeln($util->checkVersion($csf));
        $source = $util->checkSource($input);
        if ($input->getOption('diff')) {
            $source = $util->getDiffSource();
        }

        $option = ' --dry-run --diff';
        if ($input->getOption('fix')) {
            $option = '';
        }

        $standard = strtolower($input->getOption('standard'));
        $standards = '--level='.str_replace(',', ' --level=', $standard);
        $sources = explode(' ', $source);
        $exitCode = 0;
        foreach ($sources as $source) {
            $cmd = $csf.' fix '.$source.' --ansi '.$standards.$option;
            $output->writeln('<info>Command: '.$cmd.'</>');
            $style->newLine();
            $process = new Process($cmd);
            $process->setTimeout(3600)->run();
            $output->writeln($process->getOutput());

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

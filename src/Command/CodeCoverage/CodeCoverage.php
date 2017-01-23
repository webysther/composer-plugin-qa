<?php

namespace Webs\QA\Command\CodeCoverage;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;
use Webs\QA\Command\Util;

/**
 * Code Coverage Wrapper.
 */
class CodeCoverage extends BaseCommand
{
    /**
     * Console description.
     *
     * @var string
     */
    protected $description = 'Code Coverage';

    /**
     * Console params configuration.
     */
    protected function configure()
    {
        // Alias is composer qa:cc
        $this->setName('qa:code-coverage')
            ->addArgument(
                'source',
                InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
                'List of directories/files to search'
            )
            ->addOption(
                'fail-coverage-less-than',
                null,
                InputOption::VALUE_REQUIRED,
                'Fail if covered lines is less than the value.',
                80
            )
            ->addOption(
                'html',
                null,
                InputOption::VALUE_REQUIRED,
                'Dump HTML format of coverage'
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
        $style = new SymfonyStyle($input, $output);
        $style->setDecorated(true);
        $style->title($this->description);

        $util = new Util();
        $paratest = $util->checkBinary('paratest');
        $output->writeln($util->checkVersion($paratest));

        $source = '';
        if ($input->getArgument('source')) {
            $source = ' '.$util->checkSource($input);
        }

        $process = new Process('rm -rf coverage');
        $process->run();
        mkdir('coverage');

        $cmd = $paratest.$source.' --colors --coverage-php=coverage/result.cov';

        if ($input->getOption('html')) {
            $cmd = $paratest.$source.' --colors --coverage-html=coverage';
        }

        $output->writeln('<info>Command: '.$cmd.'</>');
        $process = new Process($cmd);
        $process->setTimeout(3600);
        $process->run(function ($type, $buffer) use ($style) {
            $style->write($buffer);
        });
        $exitCode = $process->getExitCode();

        if ($exitCode) {
            return $exitCode;
        }

        if ($input->getOption('html')) {
            $output->writeln('Open the file ./coverage/index.html');

            return 0;
        }

        $cov = $util->checkBinary('phpcov');
        $cmd = $cov.' merge --text --show-colors coverage';
        $style->newLine();
        $output->writeln('<info>Command: '.$cmd.'</>');
        $style->newLine();
        $process = new Process($cmd);
        $process->run(function ($type, $buffer) use ($style) {
            $style->write($buffer);
        });

        $cmd = $cov.' merge --text coverage';
        $process = new Process($cmd);
        $process->run();
        preg_match(
            '/^\s*Lines:\s*(\d+.\d+)\%/m',
            $process->getOutput(),
            $matches
        );
        $coverage = end($matches);

        $process = new Process('rm -rf coverage');
        $process->run();

        $end = microtime(true);
        $time = round($end - $start);

        $style->newLine();
        $style->section('Results');

        if (empty($coverage)) {
            $exitCode = 1;
        }

        if (!empty($coverage)) {
            $output->writeln('Coverage is '.$coverage.'%');
            $fail = $input->getOption('fail-coverage-less-than');
            if ($fail && $coverage < $fail) {
                $output->writeln('<error>Mininum coverage is '.$fail.'% actual is '.$coverage.'%</>');
                $exitCode = 1;
            }
        }

        $style->newLine();
        $output->writeln('<info>Command: '.$cmd.'</>');
        $output->writeln('<info>Time: '.$time.' seconds</>');
        $style->newLine();

        return $exitCode;
    }
}

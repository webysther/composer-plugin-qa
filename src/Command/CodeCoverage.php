<?php

namespace Webs\QA\Command;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class CodeCoverage extends BaseCommand
{
    protected $input;
    protected $output;
    protected $description = 'Code Coverage';

    protected function configure()
    {
        // Alias is composer qa:cc
        $this->setName('qa:code-coverage')
            ->addOption(
                'fail-coverage-less-than',
                null,
                InputOption::VALUE_REQUIRED,
                'Fail if covered lines is less than the value. Default:80%',
                80
            )
            ->setDescription($this->description);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $start = microtime(true);
        $this->input = $input;
        $this->output = $output;
        $this->output->writeln('<comment>Running ' . $this->description . '...</comment>');

        $cc = 'vendor/bin/paratest';
        if(!file_exists($cc)){
            $process = new Process('paratest --help');
            $process->run();
            if ($process->isSuccessful()) {
                $cc = 'paratest';
            } else {
                throw new ProcessFailedException($process);
            }
        }

        $process = new Process($cc . ' --version');
        $process->run();
        $this->output->writeln($process->getOutput());

        (new Process('rm -rf coverage'))->run();

        mkdir('coverage');
        $cmd = $cc . ' tests/Folha/Durin/Adapters/Resource/Guia --colors --coverage-php=coverage/result.cov';
        $this->output->writeln('<comment>Command executing ' . $cmd . '</comment>');
        $process = new Process($cmd);
        $process->setTimeout(3600);
        $command = $this;
        $process->run(function($type, $buffer) use($command){
            $command->output->write($buffer);
        });
        $exitCode = $process->getExitCode();

        if($exitCode){
            exit($exitCode);
        }

        $cov = 'vendor/bin/phpcov';
        if(!file_exists($cov)){
            $process = new Process('phpcov --help');
            $process->run();
            if ($process->isSuccessful()) {
                $cov = 'phpcov';
            } else {
                throw new ProcessFailedException($process);
            }
        }

        $cmd = $cov . ' merge --text --show-colors coverage';
        $process = new Process($cmd);
        $process->run(function($type, $buffer) use($command){
            $command->output->write($buffer);
        });

        $cmd = $cov . ' merge --text coverage';
        $process = new Process($cmd);
        $process->run();
        preg_match(
            '/^\s*Lines:\s*(\d+.\d+)\%/m',
            $process->getOutput(),
            $matches
        );
        $coverage = end($matches);

        $fail = $input->getOption('fail-coverage-less-than');
        if($fail && $coverage < $fail){
            $this->output->writeln('Mininum coverage is '.$fail.'% actual is '.$coverage.'%');
            $exitCode = 1;
        }

        (new Process('rm -rf coverage'))->run();

        $end = microtime(true);
        $time = round($end-$start);

        $this->output->writeln('Coverage is '.$coverage.'%');
        $this->output->writeln('<comment>Command executed ' . $cmd . '</comment>');
        $this->output->writeln('<comment>All take ' . $time . ' seconds</comment>');
        exit($exitCode);
    }
}

<?php

namespace Webs\QA\Command;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class PHPCSFixer extends BaseCommand
{
    protected $input;
    protected $output;
    protected $source = array('src','app','tests');
    protected $description = 'PHP Code Sniffer Fixer';

    protected function configure()
    {
        $this->setName('qa:php-cs-fixer')
            ->setDescription($this->description)
            ->addArgument(
                'source',
                InputArgument::IS_ARRAY|InputArgument::OPTIONAL,
                'List of directories to search  Default:src,app,tests'
            )
            ->addOption(
                'standard',
                null,
                InputOption::VALUE_REQUIRED,
                'List of standards  Default:PSR0,PSR1,PSR2,Symfony',
                'PSR0,PSR1,PSR2,Symfony'
            )
            ->addOption(
                'fix',
                null,
                InputOption::VALUE_NONE,
                'Apply fix'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $start = microtime(true);
        $this->input = $input;
        $this->output = $output;
        $this->output->writeln('<comment>Running ' . $this->description . '...</comment>');

        $csf = 'vendor/bin/php-cs-fixer';
        if(!file_exists($csf)){
            $process = new Process('php-cs-fixer --help');
            $process->run();
            if ($process->isSuccessful()) {
                $csf = 'php-cs-fixer';
            } else {
                throw new ProcessFailedException($process);
            }
        }

        $process = new Process($csf . ' --version');
        $process->run();
        $this->output->writeln($process->getOutput());

        $option = ' --dry-run --diff';
        if($input->getOption('fix')){
            $option = '';
        }

        $standards = '--level=' . str_replace(',', ' --level=', strtolower($input->getOption('standard')));
        $sources = $this->getSource();
        $exitCode = 0;
        foreach ($sources as $source) {
            $cmd = $csf . ' fix ' . $source . ' --ansi '. $standards . $option;
            $this->output->writeln('<comment>Command executing `' . $cmd . '`</comment>');
            $process = new Process($cmd);
            $process->setTimeout(3600);
            $command = $this;
            $process->run(function($type, $buffer) use($command){
                $command->output->writeln($buffer);
            });

            if(!$exitCode){
                $exitCode = $process->getExitCode();
            }
        }

        $end = microtime(true);
        $time = round($end-$start);
        $this->output->writeln('<comment>All executed in ' . $time . ' seconds</comment>');
        exit($exitCode);
    }

    protected function getSource()
    {
        if($this->input->getArgument('source')){
            $this->source = $this->input->getArgument('source');
        }

        $dirs = array();
        foreach ($this->source as $dir) {
            if(is_dir($dir)){
                $dirs[] = $dir;
            }
        }

        return $dirs;
    }
}

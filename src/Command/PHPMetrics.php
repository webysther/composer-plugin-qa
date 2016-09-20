<?php

namespace Webs\QA\Command;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class PHPMetrics extends BaseCommand
{
    protected $input;
    protected $output;
    protected $source = array('src','app','tests');
    protected $description = 'PHP Metrics';

    protected function configure()
    {
        $this->setName('qa:php-metrics')
            ->setDescription($this->description)
            ->addArgument(
                'source',
                InputArgument::IS_ARRAY|InputArgument::OPTIONAL,
                'List of directories to search  Default:src,app,tests'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $start = microtime(true);
        $this->input = $input;
        $this->output = $output;
        $this->output->writeln('<comment>Running ' . $this->description . '...</comment>');

        $pm = 'vendor/bin/phpmetrics';
        if(!file_exists($pm)){
            $process = new Process('phpmetrics --help');
            $process->run();
            if ($process->isSuccessful()) {
                $pm = 'phpmetrics';
            } else {
                throw new ProcessFailedException($process);
            }
        }

        $process = new Process($pm . ' --version');
        $process->run();
        $this->output->writeln($process->getOutput());

        $sources = $this->getSource();
        $exitCode = 0;
        foreach ($sources as $source) {
            $cmd = $pm . ' --report-cli --ansi --excluded-dirs=\'.git\' ' . $source;
            $this->output->writeln('<comment>Command executing `' . $cmd . '`</comment>');
            $process = new Process($cmd);
            $process->setTimeout(3600);
            $command = $this;
            $process->run(function($type, $buffer) use($command){
                $command->output->write($buffer);
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

<?php

namespace Webs\QA\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Command\BaseCommand;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Console\Input\InputArgument;

class LineOfCode extends BaseCommand
{
    protected $input;
    protected $output;
    protected $source = array('src','app','tests');
    protected $description = 'Line of Code';

    protected function configure()
    {
        $this->setName('qa:line-of-code')
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

        $loc = 'vendor/bin/phploc';
        if(!file_exists($loc)){
            $process = new Process('phploc --help');
            $process->run();
            if ($process->isSuccessful()) {
                $loc = 'phploc';
            } else {
                throw new ProcessFailedException($process);
            }
        }

        $cmd = $loc . ' ' . $this->getSource() . ' --ansi --count-tests';
        $process = new Process($cmd);
        $command = $this;
        $process->run(function($type, $buffer) use($command){
            $command->output->writeln($buffer);
        });
        $end = microtime(true);
        $time = round($end-$start);

        $this->output->writeln('<comment>Command executed `' . $cmd . '` in ' . $time . ' seconds</comment>');
        exit($process->getExitCode());
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

        return implode(' ', $dirs);
    }
}

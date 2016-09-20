<?php

namespace Webs\QA\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Command\BaseCommand;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class CodeSniffer extends BaseCommand
{
    protected $input;
    protected $output;
    protected $source = array('src','app','tests');
    protected $description = 'Code Sniffer';

    protected function configure()
    {
        $this->setName('qa:code-sniffer')
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
                'List of standards  Default:PSR1,PSR2',
                'PSR1,PSR2'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $start = microtime(true);
        $this->input = $input;
        $this->output = $output;
        $this->output->writeln('<comment>Running ' . $this->description . '...</comment>');

        $cs = 'vendor/bin/phpcs';
        if(!file_exists($cs)){
            $process = new Process('phpcs --help');
            $process->run();
            if ($process->isSuccessful()) {
                $cs = 'phpcs';
            } else {
                throw new ProcessFailedException($process);
            }
        }

        $process = new Process($cs . ' --version');
        $process->run();
        $this->output->writeln($process->getOutput());

        $cmd = $cs . ' ' . $this->getSource() . ' --colors --standard='.$input->getOption('standard');
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

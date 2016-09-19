<?php

namespace Webs\QA\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Command\BaseCommand;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Console\Input\InputArgument;

class CopyPasteDetector extends BaseCommand
{
    protected $input;
    protected $output;
    protected $source = array('src','app','tests');
    protected $description = 'Copy/Paste Detector';

    protected function configure()
    {
        $this->setName('qa:copy-paste-detector')
            ->setDescription($this->description)
            ->addArgument(
                'source',
                InputArgument::IS_ARRAY|InputArgument::OPTIONAL,
                'List of directories to search  Default:src,app,tests'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->output->writeln('<comment>Running ' . $this->description . '...</comment>');

        $cpd = 'vendor/bin/phpcpd';
        if(!file_exists($cpd)){
            $process = new Process('phpcpd --help');
            $process->run();
            if ($process->isSuccessful()) {
                $cpd = 'phpcpd';
            } else {
                throw new ProcessFailedException($process);
            }
        }

        $cmd = $cpd . ' ' . $this->getSource() . ' --ansi --fuzzy';
        $process = new Process($cmd);
        $command = $this;
        $process->run(function($type, $buffer) use($command){
            $command->output->writeln($buffer);
        });
        $this->output->writeln('<comment>Command executed ' . $cmd . '</comment>');
    }

    protected function getSource()
    {
        $source = $this->input->getArgument('source');
        if($source){
            $this->source = array_merge($this->source, $source);
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

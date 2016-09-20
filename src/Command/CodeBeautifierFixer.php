<?php

namespace Webs\QA\Command;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class CodeBeautifierFixer extends BaseCommand
{
    protected $input;
    protected $output;
    protected $source = array('src','app','tests');
    protected $description = 'Code Beautifier and Fixer';

    protected function configure()
    {
        // Alias is composer qa:cbf
        $this->setName('qa:code-beautifier-fixer')
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
            )
            ->addOption(
                'no-diff',
                null,
                InputOption::VALUE_NONE,
                'Ignore changed files Important: Using `git stash`, maybe fail in some cases'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $start = microtime(true);
        $this->input = $input;
        $this->output = $output;
        $this->output->writeln('<comment>Running ' . $this->description . '...</comment>');

        $cbf = 'vendor/bin/phpcbf';
        if(!file_exists($cbf)){
            $process = new Process('phpcbf --help');
            $process->run();
            if ($process->isSuccessful()) {
                $cbf = 'phpcbf';
            } else {
                throw new ProcessFailedException($process);
            }
        }

        $process = new Process($cbf . ' --version');
        $process->run();
        $this->output->writeln($process->getOutput());

        if($input->getOption('no-diff')){
            $process = new Process('git stash');
            $process->run();
            if(!$process->isSuccessful()){
                throw new ProcessFailedException($process);
            }
        }

        $cmd = $cbf . ' --standard='.$input->getOption('standard') . ' ' . $this->getSource();
        $process = new Process($cmd);
        $process->setTimeout(3600);
        $process->run();
        $exitCode = $process->getExitCode();

        $changes = true;
        if(strpos($process->getOutput(), 'No fixable errors were found') !== false){
            $changes = false;
            $this->output->writeln('No changes'.PHP_EOL);
        }

        $changed = '';
        if($changes){
            $process = new Process('git status -s');
            $process->run();
            $changed = $process->getOutput();
            $this->output->writeln($changed);
        }

        $filesChanged = count(preg_split('/\n|\r/', $changed));
        if($changes && $filesChanged < 25 && $input->getOption('no-diff')){
            $process = new Process(
                'git -c color.ui=always diff --compaction-heuristic -U0 --patch --minimal --patience'
            );
            $process->run();
            $this->output->writeln($process->getOutput());
        }

        if($input->getOption('no-diff')){
            $process = new Process('git stash apply');
            $process->run();
            if(!$process->isSuccessful()){
                throw new ProcessFailedException($process);
            }
        }

        $end = microtime(true);
        $time = round($end-$start);

        $this->output->writeln('<comment>Command executed `' . $cmd . '` in ' . $time . ' seconds</comment>');
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

        return implode(' ', $dirs);
    }
}

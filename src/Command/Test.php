<?php

namespace Webs\QA\Command;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Test extends BaseCommand
{
    protected $input;
    protected $output;
    protected $description = 'Tests';

    protected function configure()
    {
        $this->setName('qa:test')
            ->addOption(
                'stop-on-failure',
                null,
                InputOption::VALUE_NONE,
                'Stop in case of failure'
            )
            ->setDescription($this->description);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $start = microtime(true);
        $this->input = $input;
        $this->output = $output;
        $this->output->writeln('<comment>Running ' . $this->description . '...</comment>');

        $test = 'vendor/bin/phpunit';
        if(!file_exists($test)){
            $process = new Process('phpunit --help');
            $process->run();
            if ($process->isSuccessful()) {
                $test = 'phpunit';
            } else {
                throw new ProcessFailedException($process);
            }
        }

        $stopFail = '';
        if($input->getOption('stop-on-failure')){
            $stopFail = ' --stop-on-failure';
        }

        $cmd = $test . ' --colors=always' . $stopFail;
        $process = new Process($cmd);
        $process->setTimeout(3600);
        $command = $this;
        $process->run(function($type, $buffer) use($command){
            $command->output->write($buffer);
        });
        $end = microtime(true);
        $time = round($end-$start);

        $this->output->writeln('<comment>Command executed `' . $cmd . '` in ' . $time . ' seconds</comment>');
        exit($process->getExitCode());
    }
}

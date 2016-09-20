<?php

namespace Webs\QA\Command;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class All extends BaseCommand
{
    protected $input;
    protected $output;
    protected $description = 'Run all tools';

    protected function configure()
    {
        $this->setName('qa:all')
            ->setDescription($this->description);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $start = microtime(true);

        $provider = new Provider();
        $commands = $provider->getCommands();

        foreach ($commands as $command) {
            $name = $command->getName();
            if($name == 'qa:all' || strlen($name) < 7) continue;

            $output->writeln('<comment>Running ' . $name . '</comment>');
            $returnCode = $this->getApplication()->find($command->getName())->run($input, $output);
            if($returnCode){
                exit($returnCode);
            }
        }

        $end = microtime(true);
        $time = round($end-$start);
        $output->writeln('<comment>All take ' . $time . ' seconds</comment>');
    }
}

<?php

namespace Webs\QA\Command;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class All extends BaseCommand
{
    protected $description = 'Run all tools';

    protected function configure()
    {
        $this->setName('qa:all')
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
        $provider = new Provider();
        $commands = $provider->getCommands();
        $io = new SymfonyStyle($input, $output);
        $output->write(sprintf("\033\143"));
        $io->title('Running all');
        $ignore = array('qa:all', 'qa:fixer');

        foreach ($commands as $key => $command) {
            $name = $command->getName();
            if (in_array($name, $ignore) || strlen($name) < 7) {
                continue;
            }

            $arguments = array(
                'command' => $name
            );

            $startCommand = microtime(true);
            $output->write('<info>Running ' . $name . '</>');
            $returnCode = $this->getApplication()->find($command->getName())->run(
                new ArrayInput($arguments),
                new NullOutput()
            );
            $endCommand = microtime(true);
            $timeCommand = round($endCommand-$startCommand);
            $output->write(' - ' . $timeCommand . ' secs');

            if ($returnCode) {
                $output->write(' - <error>exit code ' . $returnCode . '</>');
            }

            $io->newLine();
            if ($input->getOption('stop-on-failure')) {
                return $returnCode;
            }
        }

        $end = microtime(true);
        $time = round($end-$start);
        $io->newLine();
        $io->section("Results");
        $output->writeln('<info>Time: ' . $time . ' seconds</>');
    }
}

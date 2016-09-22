<?php

/**
 * Composer Plugin QA.
 *
 * @author Webysther Nunes <webysther@gmail.com>
 */

namespace Webs\QA\Command;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Run all tests, ignore short version.
 */
class All extends BaseCommand
{
    /**
     * Console description.
     *
     * @var string
     */
    protected $description = 'Run all tools';

    /**
     * Console params configuration.
     */
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

    /**
     * Execution.
     *
     * @param InputInterface  $input  Input console
     * @param OutputInterface $output Output console
     *
     * @return int Exit code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $start = microtime(true);
        $provider = new Provider();
        $commands = $provider->getCommands();
        $style = new SymfonyStyle($input, $output);
        $output->write(sprintf("\033\143"));
        $style->title('Running all');
        $ignore = array('qa:all', 'qa:fixer', 'qa:paratest');

        foreach ($commands as $command) {
            $name = $command->getName();
            if (in_array($name, $ignore) || strlen($name) < 7) {
                continue;
            }

            $arguments = array(
                'command' => $name,
            );

            $startCommand = microtime(true);
            $output->write('<info>Running '.$name.'</>');
            $returnCode = $this->getApplication()->find($command->getName())->run(
                new ArrayInput($arguments),
                new NullOutput()
            );
            $endCommand = microtime(true);
            $timeCommand = round($endCommand - $startCommand);
            $output->write(' - '.$timeCommand.' secs');

            if ($returnCode) {
                $output->write(' - <error>exit code '.$returnCode.'</>');
            }

            $style->newLine();
            if ($input->getOption('stop-on-failure')) {
                return $returnCode;
            }
        }

        $end = microtime(true);
        $time = round($end - $start);
        $style->newLine();
        $style->section('Results');
        $output->writeln('<info>Time: '.$time.' seconds</>');
    }
}

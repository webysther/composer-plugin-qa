<?php

namespace Webs\QA\Command;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Group command to run 'Code Beautifier and Fixer' and 'PHP Code Sniffer Fixer'.
 */
class Fixer extends BaseCommand
{
    /**
     * Console description.
     *
     * @var string
     */
    protected $description = 'Run qa:code-beautifier-fixer and qa:php-cs-fixer';

    /**
     * Console params configuration.
     */
    protected function configure()
    {
        $this->setName('qa:fixer')
            ->setDescription($this->description)
            ->addArgument(
                'source',
                InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
                'List of directories/files to search <comment>[Default:"src,app,tests"]</>'
            )
            ->addOption(
                'standard',
                null,
                InputOption::VALUE_REQUIRED,
                'List of standards',
                'PSR1,PSR2'
            );
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
        $commands = array('qa:cbf', 'qa:csf');
        $style = new SymfonyStyle($input, $output);
        $style->setDecorated(true);
        $output->write(sprintf("\033\143"));

        foreach ($commands as $command) {
            $returnCode = $this->getApplication()->find($command)->run($input, $output);
            if ($returnCode) {
                $output->writeln('<error>Exit code '.$returnCode.'</>');
            }
        }

        $end = microtime(true);
        $time = round($end - $start);
        $style->newLine();
        $style->section('Results');
        $output->writeln('<info>Time: '.$time.' seconds</>');
    }
}

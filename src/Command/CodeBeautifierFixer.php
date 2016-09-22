<?php

namespace Webs\QA\Command;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class CodeBeautifierFixer extends BaseCommand
{
    protected $description = 'Code Beautifier and Fixer';

    protected function configure()
    {
        // Alias is composer qa:cbf
        $this->setName('qa:code-beautifier-fixer')
            ->setDescription($this->description)
            ->addArgument(
                'source',
                InputArgument::IS_ARRAY|InputArgument::OPTIONAL,
                'List of directories/files to search <comment>[Default:"src,app,tests"]</>'
            )
            ->addOption(
                'standard',
                null,
                InputOption::VALUE_REQUIRED,
                'List of standards',
                'PSR1,PSR2'
            )
            ->addOption(
                'diff',
                null,
                InputOption::VALUE_NONE,
                'Use `git status -s` to search files to check'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $start = microtime(true);
        $io = new SymfonyStyle($input, $output);
        $io->title($this->description);

        $util = new Util();
        $cbf = $util->checkBinary('phpcbf');
        $output->writeln($util->checkVersion($cbf));
        $source = $util->checkSource($input);
        if ($input->getOption('diff')) {
            $source = $util->getDiffSource();
        }

        if (empty($source)) {
            $output->writeln('<error>No files found</>');
            $io->newLine();
            return 1;
        }

        $cmd = $cbf . ' --standard='.$input->getOption('standard') . ' ' . $source;
        $output->writeln('<info>Command: ' . $cmd . '</>');
        $io->newLine();
        $process = new Process($cmd);
        $exitCode = $process->setTimeout(3600)->run();

        $changes = true;
        if (strpos($process->getOutput(), 'No fixable errors were found') !== false) {
            $changes = false;
            $output->writeln('<info>No changes</>');
            $io->newLine();
            return 0;
        }

        $changed = $util->getDiffSource();
        if ($changes && !empty($changed)) {
            $output->writeln(str_replace(' ', PHP_EOL, $changed));
            $sources = explode(' ', $changed);
            $io->newLine();

            foreach ($sources as $source) {
                $git = 'git -c color.ui=always';
                $options = '--compaction-heuristic -U0 --patch --minimal --patience';
                $process = new Process($git . ' diff ' . $options . ' ' . $source);
                $process->run();
                $output->writeln($process->getOutput());
            }
        }

        $end = microtime(true);
        $time = round($end-$start);

        $io->section("Results");
        $output->writeln('<info>Time: ' . $time . ' seconds</>');
        $io->newLine();
        return $exitCode;
    }
}

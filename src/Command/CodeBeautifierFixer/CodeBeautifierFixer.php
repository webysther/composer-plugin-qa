<?php

/**
 * Composer Plugin QA.
 *
 * @author Webysther Nunes <webysther@gmail.com>
 */
namespace Webs\QA\Command\CodeBeautifierFixer;

use Composer\Command\BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;
use Webs\QA\Command\Util;

/**
 * Code Beautifier and Fixer Wrapper.
 */
class CodeBeautifierFixer extends BaseCommand
{
    /**
     * Console description.
     *
     * @var string
     */
    protected $description = 'Code Beautifier and Fixer';

    /**
     * Console params configuration.
     */
    protected function configure()
    {
        // Alias is composer qa:cbf
        $this->setName('qa:code-beautifier-fixer')
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
            )
            ->addOption(
                'diff',
                null,
                InputOption::VALUE_NONE,
                'Use `git status -s` to search files to check'
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
        $style = new SymfonyStyle($input, $output);
        $style->title($this->description);

        $util = new Util();
        $cbf = $util->checkBinary('phpcbf');
        $output->writeln($util->checkVersion($cbf));
        $source = $util->checkSource($input);
        if ($input->getOption('diff')) {
            $source = $util->getDiffSource();
        }

        if (empty($source)) {
            $output->writeln('<error>No files found</>');
            $style->newLine();

            return 1;
        }

        $cmd = $cbf.' --standard='.$input->getOption('standard').' '.$source;
        $output->writeln('<info>Command: '.$cmd.'</>');
        $style->newLine();
        $process = new Process($cmd);
        $exitCode = $process->setTimeout(3600)->run();

        $changes = true;
        if (strpos($process->getOutput(), 'No fixable errors were found') !== false) {
            $changes = false;
            $output->writeln('<info>No changes</>');
            $style->newLine();

            return 0;
        }

        $changed = $util->getDiffSource();
        if ($changes && !empty($changed)) {
            $output->writeln(str_replace(' ', PHP_EOL, $changed));
            $sources = explode(' ', $changed);
            $style->newLine();

            foreach ($sources as $source) {
                $git = 'git -c color.ui=always';
                $options = '--compaction-heuristic -U0 --patch --minimal --patience';
                $process = new Process($git.' diff '.$options.' '.$source);
                $process->run();
                $output->writeln($process->getOutput());
            }
        }

        $end = microtime(true);
        $time = round($end - $start);

        $style->section('Results');
        $output->writeln('<info>Time: '.$time.' seconds</>');
        $style->newLine();

        return $exitCode;
    }
}

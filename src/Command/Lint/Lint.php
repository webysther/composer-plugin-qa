<?php

namespace Webs\QA\Command\Lint;

use Composer\Command\BaseCommand;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;
use Webs\QA\Command\Util;

/**
 * Run PHP -l (lint) for all PHP files in project.
 */
class Lint extends BaseCommand
{
    /**
     * Console description.
     *
     * @var string
     */
    protected $description = 'Lint';

    /**
     * Console params configuration.
     */
    protected function configure()
    {
        $this->setName('qa:lint')
            ->setDescription($this->description)
            ->addArgument(
                'source',
                InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
                'List of directories/files to search <comment>[Default:"src,app,tests"]</>'
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
        $style->setDecorated(true);
        $style->title($this->description);

        $util = new Util();
        $source = $util->checkSource($input);
        if ($input->getOption('diff')) {
            $source = $util->getDiffSource();
        }

        $sources = $this->getAllFiles(explode(' ', $source));
        $exitCode = 0;
        $columnSize = 80;
        $errors = '';
        foreach ($sources as $line => $source) {
            if (($line % $columnSize) == 0) {
                $style->newLine();
            }

            $cmd = 'php -l '.$source;
            $process = new Process($cmd);
            $process->setTimeout(3600)->run(function ($type, $buffer) use ($style, &$errors) {
                if (strpos($buffer, 'No syntax errors') !== false || Process::ERR == $type) {
                    $style->write('.');

                    return;
                }

                $style->write('<error>E</>');
                $errors = $buffer;
            });

            if (!$exitCode) {
                $exitCode = $process->getExitCode();
            }
        }

        $end = microtime(true);
        $time = round($end - $start);

        $style->newLine();
        $output->write($errors);
        $style->newLine();

        $style->section('Results');
        $output->writeln('<info>Command: php -l FILE</>');
        $output->writeln('<info>Files: '.count($sources).'</>');
        $output->writeln('<info>Time: '.$time.' seconds</>');
        $style->newLine();

        return $exitCode;
    }

    /**
     * Check how of list is dir and find all files.
     *
     * @param array $sources List of files/dirs
     *
     * @return array List of files only
     */
    public function getAllFiles($sources)
    {
        $files = array();
        foreach ($sources as $source) {
            if (!is_dir($source)) {
                $files[] = $source;
                continue;
            }

            $recursiveFiles = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($source)
            );
            foreach ($recursiveFiles as $file) {
                if ($file->isDir()) {
                    continue;
                }

                $path = $file->getPathname();
                $info = pathinfo(basename($path));

                if (array_key_exists('extension', $info) && $info['extension'] == 'php') {
                    $files[] = $path;
                }
            }
        }

        return $files;
    }
}

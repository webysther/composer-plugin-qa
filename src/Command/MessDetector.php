<?php

namespace Webs\QA\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Command\BaseCommand;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MessDetector extends BaseCommand
{
    protected $input;
    protected $output;
    protected $source = array('src','app','tests');
    protected $description = 'Mess Detector';

    protected function configure()
    {
        $this->setName('qa:mess-detector')
            ->setDescription($this->description)
            ->addArgument(
                'source',
                InputArgument::IS_ARRAY|InputArgument::OPTIONAL,
                'List of directories to search  Default:src,app,tests'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $start = microtime(true);
        $this->input = $input;
        $this->output = $output;
        $this->output->writeln('<comment>Running ' . $this->description . '...</comment>');

        $md = 'vendor/bin/phpmd';
        if(!file_exists($md)){
            $process = new Process('phpmd --version'); // --help return exit 1
            $process->run();
            if ($process->isSuccessful()) {
                $md = 'phpmd';
            } else {
                throw new ProcessFailedException($process);
            }
        }

        $process = new Process($md . ' --version');
        $process->run();
        $this->output->writeln($process->getOutput());

        $cmd = $md . ' ' . $this->getSource() . ' text phpmd.xml';

        $process = new Process($cmd);
        $process->setTimeout(3600);
        $process->run();
        $end = microtime(true);
        $time = round($end-$start);

        $this->output->writeln($this->format($process->getOutput()));
        $this->output->writeln('<comment>Command executed `' . $cmd . '` in ' . $time . ' seconds</comment>');
        exit($process->getExitCode());
    }

    /**
     * @todo  Make PR for this
     */
    protected function format($file)
    {
        $file = @file_get_contents("phpmd.log") ;
        $file = str_replace(PHP_EOL, " \033[0m ".PHP_EOL, $file);
        $file = str_replace(realpath(__DIR__."/..")."/", '', $file);
        $file = str_replace("\t", " \033[1;31m " . PHP_EOL, $file);
        return str_replace(". ", ".".PHP_EOL, $file);
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

        return implode(',', $dirs);
    }
}

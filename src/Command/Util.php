<?php

namespace Webs\QA\Command;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Console\Input\InputInterface;

class Util
{
    protected $source = array('src','app','tests');

    public function checkSource(InputInterface $input, $separator = ' ')
    {
        if ($input->getArgument('source')) {
            $this->source = $input->getArgument('source');
        }

        $sources = array();
        foreach ($this->source as $source) {
            if (is_dir($source) || file_exists($source)) {
                $sources[] = $source;
            }
        }

        return implode($separator, $sources);
    }

    public function getDiffSource($separator = ' ')
    {
        $process = new Process('git status -s');
        $process->run();
        $changed = $process->getOutput();
        $filesChanged = preg_split('/\n|\r/', $changed);
        $validFiles = array();

        foreach ($filesChanged as $fileLine) {
            if (empty($fileLine)) {
                continue;
            }

            list(, $file) = explode(' ', trim($fileLine));
            $info = pathinfo(basename($file));

            if (array_key_exists('extension', $info) && $info["extension"] == "php") {
                $validFiles[] = $file;
            }
        }

        return implode($separator, $validFiles);
    }

    public function checkBinary($name)
    {
        $bin = 'vendor/bin/'.$name;
        if (!file_exists($bin)) {
            $process = new Process($name.' --version');
            $process->run();
            if ($process->isSuccessful()) {
                $bin = $name;
            } else {
                throw new ProcessFailedException($process);
            }
        }

        return $bin;
    }

    public function CheckVersion($name)
    {
        $process = new Process($name . ' --version');
        $process->run();
        return $process->getOutput();
    }
}

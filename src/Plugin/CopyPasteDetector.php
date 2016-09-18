<?php

namespace Webs\QA\Plugin;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Plugin\PluginEvents;

class CopyPasteDetector implements PluginInterface
{
    protected $composer;
    protected $io;

    /**
     * Recebe os eventos internos do composer
     *
     * @param  Composer    $composer
     * @param  IOInterface $io
     * @return void
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
    }
}

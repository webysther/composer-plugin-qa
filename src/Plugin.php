<?php

namespace Webs\QA;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Plugin\Capable;

class Plugin implements PluginInterface, Capable
{
    /**
     * This will suppress UnusedFormalParameter
     * warnings in this method.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function activate(Composer $composer, IOInterface $style)
    {
    }

    public function getCapabilities()
    {
        return array(
            'Composer\Plugin\Capability\CommandProvider' => 'Webs\QA\Command\Provider',
        );
    }
}

<?php

namespace Webs\QA\Command;

use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;

class Provider implements CommandProviderCapability
{
    public function getCommands()
    {
        return array(
            new CopyPasteDetector
        );
    }
}

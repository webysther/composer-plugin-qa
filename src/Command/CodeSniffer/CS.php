<?php

namespace Webs\QA\Command\CodeSniffer;

/**
 * Alias for qa:code-sniffer.
 */
class CS extends CodeSniffer
{
    /**
     * Console params configuration.
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('qa:cs');
    }
}

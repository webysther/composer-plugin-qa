<?php

/**
 * Composer Plugin QA
 *
 * @author Webysther Nunes <webysther@gmail.com>
 */

namespace Webs\QA\Command\CodeSniffer;

/**
 * Alias for qa:code-sniffer
 */
class CS extends CodeSniffer
{
    /**
     * Console params configuration
     *
     * @return void
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('qa:cs');
    }
}

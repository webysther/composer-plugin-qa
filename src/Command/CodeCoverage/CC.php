<?php

/**
 * Composer Plugin QA
 *
 * @author Webysther Nunes <webysther@gmail.com>
 */

namespace Webs\QA\Command\CodeCoverage;

/**
 * Alias for qa:code-coverage
 */
class CC extends CodeCoverage
{
    /**
     * Console params configuration
     *
     * @return void
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('qa:cc');
    }
}

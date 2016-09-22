<?php

/**
 * Composer Plugin QA
 *
 * @author Webysther Nunes <webysther@gmail.com>
 */

namespace Webs\QA\Command;

/**
 * Alias for qa:code-coverage
 */
class CC extends CodeCoverage
{
    protected function configure()
    {
        parent::configure();
        $this->setName('qa:cc');
    }
}

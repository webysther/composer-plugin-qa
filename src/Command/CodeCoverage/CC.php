<?php

namespace Webs\QA\Command\CodeCoverage;

/**
 * Alias for qa:code-coverage.
 */
class CC extends CodeCoverage
{
    /**
     * Console params configuration.
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('qa:cc');
    }
}

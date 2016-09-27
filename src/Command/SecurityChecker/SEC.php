<?php

namespace Webs\QA\Command\SecurityChecker;

/**
 * Alias for qa:security-checker.
 */
class SEC extends SecurityChecker
{
    /**
     * Console params configuration.
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('qa:sec');
    }
}

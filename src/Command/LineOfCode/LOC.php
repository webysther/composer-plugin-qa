<?php

namespace Webs\QA\Command\LineOfCode;

/**
 * Alias for qa:line-of-code.
 */
class LOC extends LineOfCode
{
    /**
     * Console params configuration.
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('qa:loc');
    }
}

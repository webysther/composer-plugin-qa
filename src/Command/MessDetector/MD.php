<?php

namespace Webs\QA\Command\MessDetector;

/**
 * Alias for qa:mess-detector.
 */
class MD extends MessDetector
{
    /**
     * Console params configuration.
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('qa:md');
    }
}

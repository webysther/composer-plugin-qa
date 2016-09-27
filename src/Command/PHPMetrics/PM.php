<?php

namespace Webs\QA\Command\PHPMetrics;

/**
 * Alias for qa:php-metrics.
 */
class PM extends PHPMetrics
{
    /**
     * Console params configuration.
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('qa:pm');
    }
}

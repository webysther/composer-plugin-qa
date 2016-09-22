<?php

/**
 * Composer Plugin QA
 *
 * @author Webysther Nunes <webysther@gmail.com>
 */

namespace Webs\QA\Command;

/**
 * Alias for qa:php-metrics
 */
class PM extends PHPMetrics
{
    protected function configure()
    {
        parent::configure();
        $this->setName('qa:pm');
    }
}

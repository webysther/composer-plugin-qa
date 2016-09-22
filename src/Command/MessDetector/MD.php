<?php

/**
 * Composer Plugin QA
 *
 * @author Webysther Nunes <webysther@gmail.com>
 */

namespace Webs\QA\Command\MessDetector;

/**
 * Alias for qa:mess-detector
 */
class MD extends MessDetector
{
    /**
     * Console params configuration
     *
     * @return void
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('qa:md');
    }
}

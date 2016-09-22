<?php

/**
 * Composer Plugin QA
 *
 * @author Webysther Nunes <webysther@gmail.com>
 */

namespace Webs\QA\Command;

/**
 * Alias for qa:mess-detector
 */
class MD extends MessDetector
{
    protected function configure()
    {
        parent::configure();
        $this->setName('qa:md');
    }
}

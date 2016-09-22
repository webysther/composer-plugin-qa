<?php

/**
 * Composer Plugin QA
 *
 * @author Webysther Nunes <webysther@gmail.com>
 */

namespace Webs\QA\Command;

/**
 * Alias for qa:line-of-code
 */
class LOC extends LineOfCode
{
    protected function configure()
    {
        parent::configure();
        $this->setName('qa:loc');
    }
}

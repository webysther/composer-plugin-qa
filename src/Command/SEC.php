<?php

/**
 * Composer Plugin QA
 *
 * @author Webysther Nunes <webysther@gmail.com>
 */

namespace Webs\QA\Command;

/**
 * Alias for qa:security-checker
 */
class SEC extends SecurityChecker
{
    protected function configure()
    {
        parent::configure();
        $this->setName('qa:sec');
    }
}

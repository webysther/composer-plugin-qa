<?php

/**
 * Composer Plugin QA
 *
 * @author Webysther Nunes <webysther@gmail.com>
 */

namespace Webs\QA\Command;

/**
 * Alias for qa:copy-paste-detector
 */
class CPD extends CopyPasteDetector
{
    protected function configure()
    {
        parent::configure();
        $this->setName('qa:cpd');
    }
}

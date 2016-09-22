<?php

/**
 * Composer Plugin QA
 *
 * @author Webysther Nunes <webysther@gmail.com>
 */

namespace Webs\QA\Command\CopyPasteDetector;

/**
 * Alias for qa:copy-paste-detector
 */
class CPD extends CopyPasteDetector
{
    /**
     * Console params configuration
     *
     * @return void
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('qa:cpd');
    }
}

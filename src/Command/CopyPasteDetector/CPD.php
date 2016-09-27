<?php

namespace Webs\QA\Command\CopyPasteDetector;

/**
 * Alias for qa:copy-paste-detector.
 */
class CPD extends CopyPasteDetector
{
    /**
     * Console params configuration.
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('qa:cpd');
    }
}

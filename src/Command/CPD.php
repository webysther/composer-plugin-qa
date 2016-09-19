<?php

namespace Webs\QA\Command;

class CPD extends CopyPasteDetector
{

    protected function configure()
    {
        parent::configure();
        $this->setName('qa:cpd');
    }
}

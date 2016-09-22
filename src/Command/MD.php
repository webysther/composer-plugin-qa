<?php

namespace Webs\QA\Command;

class MD extends MessDetector
{
    protected function configure()
    {
        parent::configure();
        $this->setName('qa:md');
    }
}

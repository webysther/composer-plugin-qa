<?php

namespace Webs\QA\Command;

class CC extends CodeCoverage
{

    protected function configure()
    {
        parent::configure();
        $this->setName('qa:cc');
    }
}

<?php

namespace Webs\QA\Command;

class LOC extends LineOfCode
{

    protected function configure()
    {
        parent::configure();
        $this->setName('qa:loc');
    }
}

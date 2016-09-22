<?php

namespace Webs\QA\Command;

class SEC extends SecurityChecker
{
    protected function configure()
    {
        parent::configure();
        $this->setName('qa:sec');
    }
}

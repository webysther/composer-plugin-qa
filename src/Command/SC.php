<?php

namespace Webs\QA\Command;

class SC extends SecurityChecker
{

    protected function configure()
    {
        parent::configure();
        $this->setName('qa:sc');
    }
}

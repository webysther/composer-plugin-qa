<?php

namespace Webs\QA\Command;

class PM extends PHPMetrics
{

    protected function configure()
    {
        parent::configure();
        $this->setName('qa:pm');
    }
}

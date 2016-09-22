<?php

namespace Webs\QA\Command;

class CS extends CodeSniffer
{
    protected function configure()
    {
        parent::configure();
        $this->setName('qa:cs');
    }
}

<?php

namespace Webs\QA\Command;

class CSF extends PHPCSFixer
{
    protected function configure()
    {
        parent::configure();
        $this->setName('qa:csf');
    }
}

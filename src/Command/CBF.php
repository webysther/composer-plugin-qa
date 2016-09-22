<?php

namespace Webs\QA\Command;

class CBF extends CodeBeautifierFixer
{
    protected function configure()
    {
        parent::configure();
        $this->setName('qa:cbf');
    }
}

<?php

namespace Webs\QA\Command\CodeBeautifierFixer;

/**
 * Alias for qa:code-beautifier-fixer.
 */
class CBF extends CodeBeautifierFixer
{
    /**
     * Console params configuration.
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('qa:cbf');
    }
}

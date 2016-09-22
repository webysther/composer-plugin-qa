<?php

/**
 * Composer Plugin QA.
 *
 * @author Webysther Nunes <webysther@gmail.com>
 */
namespace Webs\QA\Command\PHPCSFixer;

/**
 * Alias for qa:php-cs-fixer.
 */
class CSF extends PHPCSFixer
{
    /**
     * Console params configuration.
     */
    protected function configure()
    {
        parent::configure();
        $this->setName('qa:csf');
    }
}

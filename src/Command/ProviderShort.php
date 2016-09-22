<?php

/**
 * Composer Plugin QA.
 *
 * @author Webysther Nunes <webysther@gmail.com>
 */

namespace Webs\QA\Command;

use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;
use Webs\QA\Command\CodeBeautifierFixer\CBF as CBF;
use Webs\QA\Command\CodeCoverage\CC as CC;
use Webs\QA\Command\CodeSniffer\CS as CS;
use Webs\QA\Command\CopyPasteDetector\CPD as CPD;
use Webs\QA\Command\LineOfCode\LOC as LOC;
use Webs\QA\Command\MessDetector\MD as MD;
use Webs\QA\Command\PHPCSFixer\CSF as CSF;
use Webs\QA\Command\PHPMetrics\PM as PM;
use Webs\QA\Command\SecurityChecker\SEC as SEC;

/**
 * Provider of commands with short alias.
 */
class ProviderShort implements CommandProviderCapability
{
    /**
     * Return list of commands.
     *
     * @return array List
     */
    public function getCommands()
    {
        return array(
            new All(),
            new CBF(),
            new CC(),
            new CPD(),
            new CS(),
            new CSF(),
            new LOC(),
            new MD(),
            new PM(),
            new SEC(),
        );
    }
}

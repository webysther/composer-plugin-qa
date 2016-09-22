<?php

namespace Webs\QA\Command;

use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Provider implements CommandProviderCapability
{
    public function getCommands()
    {
        return array(
            new CopyPasteDetector(),
            new CPD(),
            new LineOfCode(),
            new LOC(),
            new CodeSniffer(),
            new CS(),
            new MessDetector(),
            new MD(),
            new CodeCoverage(),
            new CC(),
            new CodeBeautifierFixer(),
            new CBF(),
            new Test(),
            new ParaTest(),
            new PHPCSFixer(),
            new CSF(),
            new PHPMetrics(),
            new PM(),
            new SecurityChecker(),
            new SEC(),
            new All(),
            new Fixer(),
        );
    }
}

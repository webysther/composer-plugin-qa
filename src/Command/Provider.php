<?php

/**
 * Composer Plugin QA
 *
 * @author Webysther Nunes <webysther@gmail.com>
 */

namespace Webs\QA\Command;

use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;
use Webs\QA\Command\CodeBeautifierFixer\CodeBeautifierFixer as CodeBeautifierFixer;
use Webs\QA\Command\CodeBeautifierFixer\CodeCoverage as CodeCoverage;
use Webs\QA\Command\CodeBeautifierFixer\CodeSniffer as CodeSniffer;
use Webs\QA\Command\CodeBeautifierFixer\CopyPasteDetector as CopyPasteDetector;
use Webs\QA\Command\CodeBeautifierFixer\LineOfCode as LineOfCode;
use Webs\QA\Command\CodeBeautifierFixer\MessDetector as MessDetector;
use Webs\QA\Command\CodeBeautifierFixer\PHPCSFixer as PHPCSFixer;
use Webs\QA\Command\CodeBeautifierFixer\PHPMetrics as PHPMetrics;
use Webs\QA\Command\CodeBeautifierFixer\SecurityChecker as SecurityChecker;
use Webs\QA\Command\CodeBeautifierFixer\Test as ParaTest;
use Webs\QA\Command\CodeBeautifierFixer\Test as Test;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Provider implements CommandProviderCapability
{
    /**
     * Return list of commands
     *
     * @return array List
     */
    public function getCommands()
    {
        return array(
            new CodeBeautifierFixer(),
            new CodeCoverage(),
            new CodeSniffer(),
            new CopyPasteDetector(),
            new Fixer(),
            new LineOfCode(),
            new MessDetector(),
            new ParaTest(),
            new PHPCSFixer(),
            new PHPMetrics(),
            new SecurityChecker(),
            new Test()
        );
    }
}

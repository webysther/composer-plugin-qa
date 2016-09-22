<?php

/**
 * Composer Plugin QA.
 *
 * @author Webysther Nunes <webysther@gmail.com>
 */
namespace Webs\QA\Command;

use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;
use Webs\QA\Command\CodeBeautifierFixer\CodeBeautifierFixer as CodeBeautifierFixer;
use Webs\QA\Command\CodeCoverage\CodeCoverage as CodeCoverage;
use Webs\QA\Command\CodeSniffer\CodeSniffer as CodeSniffer;
use Webs\QA\Command\CopyPasteDetector\CopyPasteDetector as CopyPasteDetector;
use Webs\QA\Command\LineOfCode\LineOfCode as LineOfCode;
use Webs\QA\Command\MessDetector\MessDetector as MessDetector;
use Webs\QA\Command\PHPCSFixer\PHPCSFixer as PHPCSFixer;
use Webs\QA\Command\PHPMetrics\PHPMetrics as PHPMetrics;
use Webs\QA\Command\SecurityChecker\SecurityChecker as SecurityChecker;
use Webs\QA\Command\Test\ParaTest as ParaTest;
use Webs\QA\Command\Test\Test as Test;

/**
 * Provider of commands for plugin.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Provider implements CommandProviderCapability
{
    /**
     * Return list of commands.
     *
     * @return array List
     */
    public function getCommands()
    {
        $short = new ProviderShort();

        return array_merge(
            $short->getCommands(),
            array(
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
                new Test(),
            )
        );
    }
}

<?php
declare(strict_types = 1);
namespace UniWue\UwA11yCheck\Command;

use Symfony\Component\Console\Command\Command;
use UniWue\UwA11yCheck\Check\Preset;

/**
 * Class AbstractCheckCommand
 */
abstract class AbstractCheckCommand extends Command
{
    /**
     * Saves the result to the database
     *
     * @param Preset $preset
     * @param array $result
     */
    public function saveResults(Preset $preset, array $result): void
    {
        // @todo: Implement
    }
}

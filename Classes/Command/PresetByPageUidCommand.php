<?php

declare(strict_types=1);

namespace UniWue\UwA11yCheck\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use UniWue\UwA11yCheck\Check\Result\Impact;
use UniWue\UwA11yCheck\Check\ResultSet;
use UniWue\UwA11yCheck\Service\PresetService;

/**
 * Class PresetByPageUidCommand
 */
class PresetByPageUidCommand extends AbstractCheckCommand
{
    /**
     * Configuring the command options
     */
    public function configure()
    {
        $this->addArgument(
            'preset',
            InputArgument::REQUIRED,
            'ID of the preset'
        )
            ->addArgument(
                'uid',
                InputArgument::REQUIRED,
                'UID of page to check'
            )
            ->addOption(
                'levels',
                'l',
                InputOption::VALUE_OPTIONAL,
                'Amount of levels to check'
            );
    }

    /**
     * Execute the command
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $presetService = GeneralUtility::makeInstance(PresetService::class);

        $io = new SymfonyStyle($input, $output);
        $io->title($this->getDescription());

        $presetId = $input->getArgument('preset');
        $pageUid = (int)$input->getArgument('uid');
        $levels = $input->hasOption('levels') ? (int)$input->getOption('levels') : 0;

        $preset = $presetService->getPresetById($presetId);

        if (!$preset) {
            // @extensionScannerIgnoreLine False positive
            $io->error('Preset "' . $presetId . '" not found or contains errors (check classNames!).');
            return Command::FAILURE;
        }

        $results = $preset->executeTestSuiteByPageUid($pageUid, $levels);

        /** @var ResultSet $result */
        foreach ($results as $result) {
            switch ($result->getImpact()) {
                case Impact::MINOR:
                case Impact::MODERATE:
                    $io->text('<comment>Minor or moderate accessibility issues found for ' . $result->getTable() . ':'
                        . $result->getUid() . '</comment>');
                    break;
                case Impact::SERIOUS:
                case Impact::CRITICAL:
                    $io->text('<error>Serious or critical accessibility issues found for ' . $result->getTable() . ':'
                        . $result->getUid() . '</error>');
                    break;
                default:
                    $io->text('<info>No accessibility issues found for ' . $result->getTable() . ':'
                        . $result->getUid() . '</info>');
            }
        }

        $this->saveResults($preset, $results);

        $io->success('All done!');
        return Command::SUCCESS;
    }
}

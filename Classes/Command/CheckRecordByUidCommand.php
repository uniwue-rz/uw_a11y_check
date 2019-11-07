<?php
declare(strict_types = 1);
namespace UniWue\UwA11yCheck\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Utility\DebugUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use UniWue\UwA11yCheck\Check\A11yCheck;
use UniWue\UwA11yCheck\Service\PresetService;

/**
 * Class CheckRecordByUidCommand
 */
class CheckRecordByUidCommand extends Command
{
    /**
     * Configuring the command options
     */
    public function configure()
    {
        $this
            ->setDescription('a11y check for a single record')
            ->addArgument(
                'uid',
                InputArgument::REQUIRED,
                'UID of record to check'
            )
            ->addArgument(
                'preset',
                InputArgument::REQUIRED
            );
    }

    /**
     * Execute the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $presetService = $objectManager->get(PresetService::class);

        $io = new SymfonyStyle($input, $output);
        $io->title($this->getDescription());
        $uid = (int)$input->getArgument('uid');
        $presetId = $input->getArgument('preset');

        $preset = $presetService->getPresetById($presetId);
        $a11yCheck = $objectManager->get(A11yCheck::class, $preset);
        $results = $a11yCheck->executeCheck($uid);

        DebugUtility::debug($results);

        $io->success('All done!');
    }
}

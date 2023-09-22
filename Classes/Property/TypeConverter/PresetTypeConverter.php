<?php

namespace UniWue\UwA11yCheck\Property\TypeConverter;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Error\Error;
use TYPO3\CMS\Extbase\Property\PropertyMappingConfigurationInterface;
use TYPO3\CMS\Extbase\Property\TypeConverter\AbstractTypeConverter;
use UniWue\UwA11yCheck\Check\Preset;
use UniWue\UwA11yCheck\Service\PresetService;

/**
 * Class PresetTypeConverter
 */
class PresetTypeConverter extends AbstractTypeConverter
{
    protected PresetService $presetService;

    public function injectConfigurationService(PresetService $presetService): void
    {
        $this->presetService = $presetService;
    }

    protected $sourceTypes = ['string'];

    protected $targetType = Preset::class;

    protected $priority = 1;

    public function convertFrom(
        $source,
        $targetType,
        array $convertedChildProperties = [],
        PropertyMappingConfigurationInterface $configuration = null
    ) {
        $preset = $this->presetService->getPresetById($source);

        if (!$preset) {
            return GeneralUtility::makeInstance(Error::class, 'Preset not found', 1573053017);
        }

        return $preset;
    }
}

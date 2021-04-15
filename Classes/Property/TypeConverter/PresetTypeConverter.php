<?php

namespace UniWue\UwA11yCheck\Property\TypeConverter;

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
    /**
     * @var PresetService
     */
    protected $presetService;

    /**
     * @param PresetService $presetService
     */
    public function injectConfigurationService(PresetService $presetService)
    {
        $this->presetService = $presetService;
    }

    /**
     * @var array
     */
    protected $sourceTypes = ['string'];

    /**
     * @var string
     */
    protected $targetType = Preset::class;

    /**
     * @var int
     */
    protected $priority = 1;

    /**
     * @param mixed $source
     * @param string $targetType
     * @param array $convertedChildProperties
     * @param PropertyMappingConfigurationInterface|null $configuration
     * @return mixed|object|Error|Preset
     */
    public function convertFrom(
        $source,
        $targetType,
        array $convertedChildProperties = [],
        PropertyMappingConfigurationInterface $configuration = null
    ) {
        $preset = $this->presetService->getPresetById($source);

        if (!$preset) {
            return $this->objectManager->get(Error::class, 'Preset not found', 1573053017102);
        }

        return $preset;
    }
}

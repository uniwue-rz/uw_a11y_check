<?php
namespace UniWue\UwA11yCheck\Service;

use TYPO3\CMS\Core\Configuration\Loader\YamlFileLoader;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use UniWue\UwA11yCheck\Analyzers\AbstractAnalyzer;
use UniWue\UwA11yCheck\Check\Preset;
use UniWue\UwA11yCheck\CheckUrlGenerators\AbstractCheckUrlGenerator;

/**
 * Class PresetService
 */
class PresetService
{
    /**
     * @var YamlFileLoader
     */
    protected $yamlFileLoader = null;

    /**
     * @var ObjectManager
     */
    protected $objectManager = null;

    public function __construct()
    {
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
    }

    /**
     * @param YamlFileLoader $yamlFileLoader
     */
    public function injectYamlFileLoader(YamlFileLoader $yamlFileLoader)
    {
        $this->yamlFileLoader = $yamlFileLoader;
    }

    /**
     * Returns all presets
     *
     * @return array
     */
    public function getPresets(): array
    {
        $yamlFile = 'EXT:uw_a11y_check/Configuration/A11y/Default.yaml';
        $yamlData = $this->yamlFileLoader->load($yamlFile);

        $presets = [];

        foreach ($yamlData['presets'] as $id => $presetData) {
            $name = $presetData['name'] ?? 'No name given';

            $analyzerConfig = $this->getConfiguration($presetData, 'analyzer', $yamlData);
            $analyzer = $this->getAnalyzerById($presetData['analyzer']['id'], $yamlData, $analyzerConfig);

            $checkUrlGeneratorConfig = $this->getConfiguration($presetData, 'checkUrlGenerator', $yamlData);
            $checkUrlGenerator = $this->getCheckUrlGeneratorById(
                $presetData['checkUrlGenerator']['id'],
                $yamlData,
                $checkUrlGeneratorConfig
            );

            $configuration = $presetData['configuration'];

            $presets[] = new Preset($id, $name, $analyzer, $checkUrlGenerator, $configuration);
        }

        return $presets;
    }

    /**
     * Returns a preset by the ID
     *
     * @param string $id
     * @return Preset|null
     */
    public function getPresetById(string $id): ?Preset
    {
        $result = null;

        $presets = $this->getPresets();
        foreach ($presets as $preset) {
            if ($preset->getId() === $id) {
                $result = $preset;
                break;
            }
        }
        return $result;
    }

    /**
     * Returns an analyzer by the ID
     *
     * @param string $id
     * @param array $yamlData
     * @param array $configuration
     * @return AbstractAnalyzer|null
     */
    protected function getAnalyzerById(string $id, array $yamlData, array $configuration): ?AbstractAnalyzer
    {
        $analyzer = null;
        foreach ($yamlData['analyzers'] as $analyzerId => $analyzerConfig) {
            if ($analyzerId === $id) {
                $analyzer = $this->objectManager->get($analyzerConfig['className'], $configuration);
                break;
            }
        }

        // @todo: if no analyzer, throw an exception

        return $analyzer;
    }

    /**
     * Returns a CheckUrlGenerator by ID
     *
     * @param string $id
     * @param array $yamlData
     * @param array $configuration
     * @return AbstractCheckUrlGenerator|null
     */
    protected function getCheckUrlGeneratorById(
        string $id,
        array $yamlData,
        array $configuration
    ): ?AbstractCheckUrlGenerator {
        $checkUrlGenerator = null;
        foreach ($yamlData['checkUrlGenerators'] as $checkUrlGeneratorId => $checkUrlGeneratorConfig) {
            if ($checkUrlGeneratorId === $id) {
                $checkUrlGenerator = $this->objectManager->get(
                    $checkUrlGeneratorConfig['className'],
                    $configuration
                );
                break;
            }
        }

        // @todo: if no analyzer, throw an exception

        return $checkUrlGenerator;
    }

    /**
     * Merges local and global configuration and returns the result for the given type
     *
     * @param array $presetData
     * @param string $type
     * @param array $yamlData
     * @return array
     */
    protected function getConfiguration(array $presetData, string $type, array $yamlData): array
    {
        $globalConfiguration = $yamlData[$type . 's'][$presetData[$type]['id']]['configuration'] ?? [];
        $localConfiguration = $presetData[$type]['configuration'] ?? [];
        ArrayUtility::mergeRecursiveWithOverrule($globalConfiguration, $localConfiguration);

        return $globalConfiguration;
    }
}

<?php
namespace UniWue\UwA11yCheck\Service;

use TYPO3\CMS\Core\Configuration\Loader\YamlFileLoader;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\Container\Exception\UnknownObjectException;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use UniWue\UwA11yCheck\Analyzers\AbstractAnalyzer;
use UniWue\UwA11yCheck\Check\Preset;
use UniWue\UwA11yCheck\Check\TestSuite;
use UniWue\UwA11yCheck\CheckUrlGenerators\AbstractCheckUrlGenerator;
use UniWue\UwA11yCheck\Tests\TestInterface;
use UniWue\UwA11yCheck\Utility\Exception\ConfigurationFileNotFoundException;

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

    /**
     * @var FlashMessageService
     */
    protected $flashMessageService = null;

    /**
     * PresetService constructor.
     */
    public function __construct()
    {
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->flashMessageService = $this->objectManager->get(FlashMessageService::class);
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
        $yamlFile = $this->getConfigurationFile();
        $yamlData = $this->yamlFileLoader->load($yamlFile);

        $presets = [];

        foreach ($yamlData['presets'] as $id => $presetData) {
            $name = $presetData['name'] ?? 'No name given';

            try {
                $analyzerConfig = $this->getConfiguration($presetData, 'analyzer', $yamlData);
                $analyzer = $this->getAnalyzerById($presetData['analyzer']['id'], $yamlData, $analyzerConfig);

                $checkUrlGeneratorConfig = $this->getConfiguration($presetData, 'checkUrlGenerator', $yamlData);
                $checkUrlGenerator = $this->getCheckUrlGeneratorById(
                    $presetData['checkUrlGenerator']['id'],
                    $yamlData,
                    $checkUrlGeneratorConfig
                );

                $testSuite = $this->getTestSuiteById(
                    $presetData['testSuite']['id'],
                    $yamlData,
                    $presetData['testSuite']
                );

                $configuration = $presetData['configuration'];

                $presets[] = new Preset($id, $name, $analyzer, $checkUrlGenerator, $testSuite, $configuration);
            } catch (UnknownObjectException $exception) {
                $message = new FlashMessage(
                    $exception->getMessage(),
                    'Class not found in preset "' . $name . '"',
                    FlashMessage::ERROR,
                    true
                );
                // @extensionScannerIgnoreLine False positive
                $this->flashMessageService->getMessageQueueByIdentifier(
                    'extbase.flashmessages.tx_uwa11ycheck_web_uwa11ychecktxuwa11ycheckm1'
                )->addMessage($message);
            }
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
     * Returns a testsuite by the ID
     *
     * @param string $id
     * @param array $yamlData
     * @param array $configuration
     * @return TestSuite
     */
    protected function getTestSuiteById(string $id, array $yamlData, array $configuration): TestSuite
    {
        $testSuite = $this->objectManager->get(TestSuite::class);
        foreach ($yamlData['testSuites'] as $testSuiteId => $testSuiteTests) {
            if ($testSuiteId === $id) {
                foreach ($testSuiteTests['tests'] as $testId => $test) {
                    $globalConfiguration = $test['configuration'] ?? [];
                    $localConfiguration = $configuration['tests'][$testId]['configuration'] ?? [];
                    ArrayUtility::mergeRecursiveWithOverrule($globalConfiguration, $localConfiguration);

                    /** @var TestInterface $test */
                    $test = $this->objectManager->get($test['className'], $globalConfiguration);
                    $testSuite->addTest($test);
                }
                break;
            }
        }

        return $testSuite;
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

    protected function getConfigurationFile()
    {
        $file = $GLOBALS['TYPO3_CONF_VARS']['UwA11yCheck']['Configuration'];
        if (!file_exists(GeneralUtility::getFileAbsFileName($file))) {
            throw new ConfigurationFileNotFoundException(
                'Configured yaml "' . $file . '" configuration does not exist.',
                1573216092216
            );
        }

        return $file;
    }
}

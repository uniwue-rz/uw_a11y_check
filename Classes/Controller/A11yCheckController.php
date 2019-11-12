<?php
namespace UniWue\UwA11yCheck\Controller;

use TYPO3\CMS\Backend\Template\Components\Menu\Menu;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Property\TypeConverter\PersistentObjectConverter;
use UniWue\UwA11yCheck\Check\ResultSet;
use UniWue\UwA11yCheck\Domain\Model\Dto\CheckDemand;
use UniWue\UwA11yCheck\Service\PresetService;
use UniWue\UwA11yCheck\Service\SerializationService;

class A11yCheckController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * @var SerializationService
     */
    protected $serializationService = null;

    /**
     * @param SerializationService $serializationService
     */
    public function injectSerializationService(\UniWue\UwA11yCheck\Service\SerializationService $serializationService)
    {
        $this->serializationService = $serializationService;
    }

    /**
     * Backend Template Container
     *
     * @var string
     */
    protected $defaultViewObjectName = \TYPO3\CMS\Backend\View\BackendTemplateView::class;

    /**
     * The current page uid
     *
     * @var int
     */
    protected $pid = 0;

    /**
     * BackendTemplateContainer
     *
     * @var BackendTemplateView
     */
    protected $view;

    /**
     * @var PresetService
     */
    protected $presetService = null;

    /**
     * @param PresetService $presetService
     */
    public function injectConfigurationService(PresetService $presetService)
    {
        $this->presetService = $presetService;
    }

    /**
     * Set up the doc header properly here
     *
     * @param ViewInterface $view
     *
     * @return void
     */
    protected function initializeView(ViewInterface $view)
    {
        /** @var BackendTemplateView $view */
        parent::initializeView($view);

        $this->view->getModuleTemplate()->setFlashMessageQueue($this->controllerContext->getFlashMessageQueue());
        if ($view instanceof BackendTemplateView) {
            $view->getModuleTemplate()->getPageRenderer()->addCssFile(
                'EXT:uw_a11y_check/Resources/Public/Css/a11y_check.css'
            );
        }

        $this->createMenu();
    }

    /**
     * Initialize action
     *
     * @return void
     */
    public function initializeAction()
    {
        $this->pid = (int)\TYPO3\CMS\Core\Utility\GeneralUtility::_GET('id');
    }

    /**
     * Index action
     *
     * @param CheckDemand|null $checkDemand
     * @return void
     */
    public function indexAction(?CheckDemand $checkDemand = null): void
    {
        if (!$checkDemand) {
            $checkDemand = new CheckDemand();
        }

        // If form has been submitted, redirect to check action
        if ($checkDemand->getAnalyze() !== '') {
            $this->redirect(
                'check',
                null,
                null,
                [
                    'checkDemand' => $checkDemand->toArray()
                ]
            );
        }

        $this->createMenu();

        $this->view->assignMultiple([
            'checkDemand' => $checkDemand,
            'presets' => $this->presetService->getPresets(),
            'levelSelectorOptions' => $this->getLevelSelectorOptions(),
        ]);
    }

    /**
     * Ensure checkDemand array will be converted to an object
     *
     * @return void
     */
    public function initializeCheckAction(): void
    {
        if ($this->arguments->hasArgument('checkDemand')) {
            $propertyMappingConfiguration = $this->arguments->getArgument('checkDemand')
                ->getPropertyMappingConfiguration();
            $propertyMappingConfiguration->allowAllProperties();
            $propertyMappingConfiguration->setTypeConverterOption(
                PersistentObjectConverter::class,
                PersistentObjectConverter::CONFIGURATION_CREATION_ALLOWED,
                true
            );
        }
    }

    /**
     * Check action
     *
     * @param CheckDemand $checkDemand
     * @return void
     */
    public function checkAction(CheckDemand $checkDemand): void
    {
        $preset = $checkDemand->getPreset();
        $results = $preset->executeTestSuiteByPageUid($this->pid, $checkDemand->getLevel());

        $this->view->assignMultiple([
            'checkDemand' => $checkDemand,
            'results' => $results
        ]);
    }

    public function resultsAction(): void
    {
        $results = $this->getResultsByPid($this->pid);

        $this->view->assignMultiple([
            'results' => $results
        ]);
    }

    /**
     * Create menu
     *
     */
    protected function createMenu()
    {
        $uriBuilder = $this->objectManager->get(UriBuilder::class);
        $uriBuilder->setRequest($this->request);

        $menu = $this->view->getModuleTemplate()->getDocHeaderComponent()->getMenuRegistry()->makeMenu();
        $menu->setIdentifier('uw_a11y_check');

        $actions = ['index', 'results'];

        foreach ($actions as $action) {
            $langId = 'LLL:EXT:uw_a11y_check/Resources/Private/Language/locallang_be.xlf:module.';
            $item = $menu->makeMenuItem()
                ->setTitle($this->getLanguageService()->sL($langId . $action))
                ->setHref($uriBuilder->reset()->uriFor($action, [], 'A11yCheck'))
                ->setActive($this->request->getControllerActionName() === $action);
            $menu->addMenuItem($item);
        }

        if ($menu instanceof Menu) {
            $this->view->getModuleTemplate()->getDocHeaderComponent()->getMenuRegistry()->addMenu($menu);
        }
    }

    /**
     * @return array
     */
    protected function getLevelSelectorOptions(): array
    {
        $langId = 'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:';
        $availableOptions = [
            0 => $this->getLanguageService()->sL($langId. 'labels.depth_0'),
            1 => $this->getLanguageService()->sL($langId. 'labels.depth_1'),
            2 => $this->getLanguageService()->sL($langId. 'labels.depth_2'),
            3 => $this->getLanguageService()->sL($langId. 'labels.depth_3'),
            4 => $this->getLanguageService()->sL($langId. 'labels.depth_4'),
            999 => $this->getLanguageService()->sL($langId. 'labels.depth_infi')
        ];
        return $availableOptions;
    }

    /**
     * @param int $pid
     * @return array
     */
    protected function getResultsByPid(int $pid): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_a11ycheck_result');
        $queryBuilder->getRestrictions()->removeAll();
        $query = $queryBuilder
            ->select('*')
            ->from('tx_a11ycheck_result')
            ->where(
                $queryBuilder->expr()->eq(
                    'pid',
                    $queryBuilder->createNamedParameter($pid, \PDO::PARAM_INT)
                )
            );

        $queryResult = $query->execute()->fetchAll();
        $resultSets = [];

        foreach ($queryResult as $result) {
            $unserializedData = $this->serializationService->getSerializer()->deserialize(
                $result['resultset'],
                ResultSet::class,
                'json'
            );

            $resultSets[] = $unserializedData;
        }

        return $resultSets;
    }

    /**
     * Returns LanguageService
     *
     * @return \TYPO3\CMS\Lang\LanguageService
     */
    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }
}

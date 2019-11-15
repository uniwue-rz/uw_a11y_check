<?php
namespace UniWue\UwA11yCheck\Controller;

use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\Components\Menu\Menu;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Property\TypeConverter\PersistentObjectConverter;
use UniWue\UwA11yCheck\Domain\Model\Dto\CheckDemand;
use UniWue\UwA11yCheck\Service\PresetService;
use UniWue\UwA11yCheck\Service\ResultsService;

/**
 * Class A11yCheckController
 */
class A11yCheckController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    const LANG_CORE = 'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:';
    const LANG_LOCAL = 'LLL:EXT:uw_a11y_check/Resources/Private/Language/locallang.xlf:';

    /**
     * @var PresetService
     */
    protected $presetService = null;

    /**
     * @param PresetService $presetService
     */
    public function injectPresetService(PresetService $presetService)
    {
        $this->presetService = $presetService;
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
     * @var IconFactory
     */
    protected $iconFactory = null;

    /**
     * @var ResultsService
     */
    protected $resultsService = null;

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

        $this->iconFactory = GeneralUtility::makeInstance(IconFactory::class);
        $this->resultsService = $this->objectManager->get(ResultsService::class);

        $this->view->getModuleTemplate()->setFlashMessageQueue($this->controllerContext->getFlashMessageQueue());
        if ($view instanceof BackendTemplateView) {
            $view->getModuleTemplate()->getPageRenderer()->addCssFile(
                'EXT:uw_a11y_check/Resources/Public/Css/a11y_check.css'
            );
        }

        $this->createMenu();
        $this->createDefaultButtons();
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

        $this->view->assignMultiple([
            'checkDemand' => $checkDemand,
            'presets' => $this->presetService->getPresets(),
            'levelSelectorOptions' => $this->getLevelSelectorOptions(),
            'savedResultsCount' => $this->resultsService->getSavedResultsCount($this->pid),
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
            'results' => $results,
            'date' => new \DateTime()
        ]);
    }

    /**
     * Results action
     * @return void
     */
    public function resultsAction(): void
    {
        $this->createAcknowledgeButton($this->pid);
        $resultsArray = $this->resultsService->getResultsArrayByPid($this->pid);

        $this->view->assignMultiple([
            'resultsArray' => $resultsArray
        ]);
    }

    /**
     * AcknowledgeResult Action
     *
     * @param int $pageUid
     */
    public function acknowledgeResultAction(int $pageUid)
    {
        $this->resultsService->deleteSavedResults($pageUid);
        return $this->redirect('index');
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
            $item = $menu->makeMenuItem()
                ->setTitle($this->getLanguageService()->sL(self::LANG_LOCAL . 'module.' . $action))
                ->setHref($uriBuilder->reset()->uriFor($action, [], 'A11yCheck'))
                ->setActive($this->request->getControllerActionName() === $action);
            $menu->addMenuItem($item);
        }

        if ($menu instanceof Menu) {
            $this->view->getModuleTemplate()->getDocHeaderComponent()->getMenuRegistry()->addMenu($menu);
        }
    }

    /**
     * Creates default buttons for the module
     *
     * @return void
     */
    protected function createDefaultButtons(): void
    {
        $buttonBar = $this->view->getModuleTemplate()->getDocHeaderComponent()->getButtonBar();

        // Shortcut
        if ($this->getBackendUser()->mayMakeShortcut()) {
            $shortcutButton = $buttonBar->makeShortcutButton()
                ->setModuleName('web_UwA11yCheckTxUwa11ycheckM1')
                ->setGetVariables(['route', 'module', 'id'])
                ->setDisplayName('Shortcut');
            $buttonBar->addButton($shortcutButton, ButtonBar::BUTTON_POSITION_RIGHT);
        }
    }

    /**
     * Creates the acknowledge button
     *
     * @param int $pid
     * @return void
     */
    protected function createAcknowledgeButton(int $pid): void
    {
        $uriBuilder = $this->objectManager->get(UriBuilder::class);
        $uriBuilder->setRequest($this->request);

        $buttonBar = $this->view->getModuleTemplate()->getDocHeaderComponent()->getButtonBar();

        $title = $this->getLanguageService()->sL(self::LANG_LOCAL . 'labels.acknowledgeResults');
        $button = $buttonBar->makeLinkButton();
        $button->setHref($uriBuilder->reset()->setRequest($this->request)
            ->uriFor('acknowledgeResult', ['pageUid' => $pid], 'A11yCheck'))
            ->setDataAttributes([
                'toggle' => 'tooltip',
                'placement' => 'bottom',
                'title' => $title
            ])
            ->setTitle($title)
            ->setShowLabelText(true)
            ->setIcon($this->iconFactory->getIcon('actions-check', Icon::SIZE_SMALL));
        $buttonBar->addButton($button, ButtonBar::BUTTON_POSITION_LEFT, 2);
    }

    /**
     * @return array
     */
    protected function getLevelSelectorOptions(): array
    {
        $availableOptions = [
            0 => $this->getLanguageService()->sL(self::LANG_CORE. 'labels.depth_0'),
            1 => $this->getLanguageService()->sL(self::LANG_CORE. 'labels.depth_1'),
            2 => $this->getLanguageService()->sL(self::LANG_CORE. 'labels.depth_2'),
            3 => $this->getLanguageService()->sL(self::LANG_CORE. 'labels.depth_3'),
            4 => $this->getLanguageService()->sL(self::LANG_CORE. 'labels.depth_4'),
            999 => $this->getLanguageService()->sL(self::LANG_CORE. 'labels.depth_infi')
        ];
        return $availableOptions;
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

    /**
     * Get backend user
     *
     * @return BackendUserAuthentication
     */
    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}

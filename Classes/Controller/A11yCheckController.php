<?php

namespace UniWue\UwA11yCheck\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\Components\Menu\Menu;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Imaging\IconSize;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Annotation\IgnoreValidation;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Property\TypeConverter\PersistentObjectConverter;
use UniWue\UwA11yCheck\Domain\Model\Dto\CheckDemand;
use UniWue\UwA11yCheck\Property\TypeConverter\PresetTypeConverter;
use UniWue\UwA11yCheck\Service\PresetService;
use UniWue\UwA11yCheck\Service\ResultsService;

/**
 * Class A11yCheckController
 */
class A11yCheckController extends ActionController
{
    public const LANG_CORE = 'LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:';
    public const LANG_LOCAL = 'LLL:EXT:uw_a11y_check/Resources/Private/Language/locallang.xlf:';

    protected int $pid = 0;

    public function __construct(
        protected readonly ModuleTemplateFactory $moduleTemplateFactory,
        protected readonly PresetService $presetService,
        protected readonly PageRenderer $pageRenderer,
        protected readonly IconFactory $iconFactory,
        protected readonly ResultsService $resultsService
    ) {
    }

    /**
     * Initializes module template and returns a response which must be used as response for any extbase action
     * that should render a view.
     */
    protected function initModuleTemplateAndReturnResponse(string $templateFileName, array $variables = []): ResponseInterface
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $this->pageRenderer->addCssFile('EXT:uw_a11y_check/Resources/Public/Css/a11y_check.css');

        $this->registerDocHeaderButtons($moduleTemplate);
        $this->createMenu($moduleTemplate);

        $moduleTemplate->setFlashMessageQueue($this->getFlashMessageQueue());

        $variables['settings'] = $this->settings;
        $moduleTemplate->assignMultiple($variables);

        return $moduleTemplate->renderResponse($templateFileName);
    }

    public function initializeAction(): void
    {
        $this->pid = (int)($this->request->getQueryParams()['id'] ?? null);
    }

    /**
     * Index action
     */
    #[IgnoreValidation(['argumentName' => 'checkDemand'])]
    public function indexAction(?CheckDemand $checkDemand = null): ResponseInterface
    {
        if (!$checkDemand) {
            $checkDemand = new CheckDemand();
        }

        // If form has been submitted, redirect to check action
        if ($checkDemand->getAnalyze() !== '') {
            return $this->redirect(
                'check',
                null,
                null,
                [
                    'checkDemand' => $checkDemand->toArray(),
                ]
            );
        }

        $site = $this->request->getAttribute('site');
        $variables = [
            'checkDemand' => $checkDemand,
            'presets' => $this->presetService->getPresets($site),
            'levelSelectorOptions' => $this->getLevelSelectorOptions(),
            'savedResultsCount' => $this->resultsService->getSavedResultsCount($this->pid),
        ];

        return $this->initModuleTemplateAndReturnResponse('A11yCheck/Index', $variables);
    }

    /**
     * Ensure checkDemand array will be converted to an object
     */
    protected function initializeIndexAction(): void
    {
        $this->initializeTypeConverterForArgument();
    }

    /**
     * Ensure checkDemand array will be converted to an object
     */
    protected function initializeCheckAction(): void
    {
        $this->initializeTypeConverterForArgument();
    }

    private function initializeTypeConverterForArgument(): void
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

            $propertyMappingConfiguration->forProperty('preset')->setTypeConverterOption(
                PresetTypeConverter::class,
                PresetTypeConverter::CONFIGURATION_REQUEST,
                $this->request
            );
            $presetTypeConverter = GeneralUtility::makeInstance(PresetTypeConverter::class);
            $propertyMappingConfiguration->forProperty('preset')->setTypeConverter($presetTypeConverter);
        }
    }

    /**
     * Check action
     */
    #[IgnoreValidation(['argumentName' => 'checkDemand'])]
    public function checkAction(CheckDemand $checkDemand): ResponseInterface
    {
        $preset = $checkDemand->getPreset();
        $results = $preset->executeTestSuiteByPageUid($this->pid, $checkDemand->getLevel());

        $variables = [
            'checkDemand' => $checkDemand,
            'results' => $results,
            'date' => new \DateTime(),
        ];

        return $this->initModuleTemplateAndReturnResponse('A11yCheck/Check', $variables);
    }

    /**
     * Results action
     */
    public function resultsAction(): ResponseInterface
    {
        $resultsArray = $this->resultsService->getResultsArrayByPid($this->pid);

        $variables = [
            'resultsArray' => $resultsArray,
        ];

        return $this->initModuleTemplateAndReturnResponse('A11yCheck/Results', $variables);
    }

    /**
     * AcknowledgeResult Action
     */
    public function acknowledgeResultAction(int $pageUid): ResponseInterface
    {
        $this->resultsService->deleteSavedResults($pageUid);
        return $this->redirect('index');
    }

    /**
     * Register docHeaderButtons
     */
    protected function registerDocHeaderButtons(ModuleTemplate $moduleTemplate): void
    {
        $buttonBar = $moduleTemplate->getDocHeaderComponent()->getButtonBar();

        $buttons = [];
        if ($this->request->getControllerActionName() === 'results') {
            $buttons[] = [
                'label' => 'labels.acknowledgeResults',
                'link' => $this->uriBuilder->reset()->setRequest($this->request)
                    ->uriFor('acknowledgeResult', ['pageUid' => $this->pid], 'A11yCheck'),
                'icon' => 'actions-check',
                'group' => 1,
            ];
        }

        foreach ($buttons as $tableConfiguration) {
            $title = $this->getLanguageService()->sL(self::LANG_LOCAL . $tableConfiguration['label']);
            $icon = $this->iconFactory->getIcon($tableConfiguration['icon'], IconSize::SMALL);
            $viewButton = $buttonBar->makeLinkButton()
                ->setHref($tableConfiguration['link'])
                ->setDataAttributes([
                    'toggle' => 'tooltip',
                    'placement' => 'bottom',
                    'title' => $title,
                ])
                ->setTitle($title)
                ->setIcon($icon);
            $buttonBar->addButton($viewButton, ButtonBar::BUTTON_POSITION_LEFT, $tableConfiguration['group']);
        }

        $shortcutButton = $buttonBar->makeShortcutButton()
            ->setRouteIdentifier('web_UwA11yCheckTxUwa11ycheckM1')
            ->setDisplayName('A11Y Check')
            ->setArguments(['id' => $this->pid]);
        $buttonBar->addButton($shortcutButton, ButtonBar::BUTTON_POSITION_RIGHT);
    }

    /**
     * Create menu
     */
    protected function createMenu(ModuleTemplate $moduleTemplate): void
    {
        $menu = $moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->makeMenu();
        $menu->setIdentifier('uw_a11y_check');

        $actions = ['index', 'results'];

        foreach ($actions as $action) {
            $item = $menu->makeMenuItem()
                ->setTitle($this->getLanguageService()->sL(self::LANG_LOCAL . 'module.' . $action))
                ->setHref($this->uriBuilder->reset()->uriFor($action, [], 'A11yCheck'))
                ->setActive($this->request->getControllerActionName() === $action);
            $menu->addMenuItem($item);
        }

        if ($menu instanceof Menu) {
            $moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->addMenu($menu);
        }
    }

    protected function getLevelSelectorOptions(): array
    {
        return [
            0 => $this->getLanguageService()->sL(self::LANG_CORE . 'labels.depth_0'),
            1 => $this->getLanguageService()->sL(self::LANG_CORE . 'labels.depth_1'),
            2 => $this->getLanguageService()->sL(self::LANG_CORE . 'labels.depth_2'),
            3 => $this->getLanguageService()->sL(self::LANG_CORE . 'labels.depth_3'),
            4 => $this->getLanguageService()->sL(self::LANG_CORE . 'labels.depth_4'),
            999 => $this->getLanguageService()->sL(self::LANG_CORE . 'labels.depth_infi'),
        ];
    }

    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }

    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}

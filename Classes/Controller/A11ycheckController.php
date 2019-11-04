<?php
namespace UniWue\UwA11yCheck\Controller;

use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\Utility\DebugUtility;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use UniWue\UwA11yCheck\Check\A11yCheck;
use UniWue\UwA11yCheck\Service\A11yCheckService;
use UniWue\UwA11yCheck\Service\PresetService;

class A11ycheckController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
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
     * @var A11yCheckService
     */
    protected $a11yCheckService = null;

    /**
     * @var PresetService
     */
    protected $configurationService = null;

    /**
     * @param A11yCheckService $A11yCheckService
     */
    public function injectA11yCheckService(\UniWue\UwA11yCheck\Service\A11yCheckService $a11yCheckService)
    {
        $this->a11yCheckService = $a11yCheckService;
    }

    /**
     * @param PresetService $configurationService
     */
    public function injectConfigurationService(\UniWue\UwA11yCheck\Service\PresetService $configurationService)
    {
        $this->configurationService = $configurationService;
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

    public function indexAction()
    {
        $presets = $this->configurationService->getPresets();
    }

    public function checkAction()
    {
        $preset = $this->configurationService->getPresetById('preset1');
        $a11yCheck = new A11yCheck($preset);
        DebugUtility::debug($a11yCheck->executeCheck($this->pid));
    }
}

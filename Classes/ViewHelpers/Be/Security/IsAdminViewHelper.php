<?php
namespace UniWue\UwA11yCheck\ViewHelpers\Be\Security;

/**
 * Class isAdminViewHelper
 *
 * Returns, if the current backend user is admin
 */
class IsAdminViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractConditionViewHelper
{
    /**
     * Checks if the current backend user is admin
     *
     * @param array $arguments
     * @return bool
     */
    protected static function evaluateCondition($arguments = null)
    {
        return $GLOBALS['BE_USER']->isAdmin();
    }

    /**
     * @return mixed
     */
    public function render()
    {
        if (static::evaluateCondition($this->arguments)) {
            return $this->renderThenChild();
        }
        return $this->renderElseChild();
    }
}
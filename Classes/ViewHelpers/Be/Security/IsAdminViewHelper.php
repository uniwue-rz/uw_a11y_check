<?php

namespace UniWue\UwA11yCheck\ViewHelpers\Be\Security;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

/**
 * Class isAdminViewHelper
 *
 * Returns, if the current backend user is admin
 */
class IsAdminViewHelper extends AbstractConditionViewHelper
{
    /**
     * Checks if the current backend user is admin
     */
    protected static function evaluateCondition($arguments = null)
    {
        return $GLOBALS['BE_USER']->isAdmin();
    }

    public function render()
    {
        if (static::evaluateCondition($this->arguments)) {
            return $this->renderThenChild();
        }
        return $this->renderElseChild();
    }
}

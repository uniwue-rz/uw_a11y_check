<?php
namespace UniWue\UwA11yCheck\ViewHelpers\Be\Link;

/**
 * ViewHelper for a backend link that should edit the given event UID
 */
class EditRecordViewHelper extends AbstractRecordViewHelper
{
    /**
     * Initialize arguments
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('table', 'string', 'Name of the table', true);
        $this->registerArgument('uid', 'integer', 'Record UID', true);
    }

    /**
     * Renders a edit link for the given Event UID
     *
     * @return string
     */
    public function render()
    {
        $uid = $this->arguments['uid'];
        $table = $this->arguments['table'];
        $parameters = [
            'edit[' . $table . '][' . (int)$uid . ']' => 'edit',
            'returnUrl' => $this->getReturnUrl(),
        ];

        return $this->getModuleUrl($parameters);
    }
}

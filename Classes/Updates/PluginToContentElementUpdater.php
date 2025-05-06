<?php

declare(strict_types=1);

namespace UniWue\UwA11yCheck\Updates;

use TYPO3\CMS\Install\Attribute\UpgradeWizard;
use TYPO3\CMS\Install\Updates\AbstractListTypeToCTypeUpdate;

#[UpgradeWizard('uwA11yCheckPluginToContentElementUpdate')]
class PluginToContentElementUpdater extends AbstractListTypeToCTypeUpdate
{
    protected function getListTypeToCTypeMapping(): array
    {
        return [
            'uwa11ycheck_pi1' => 'uwa11ycheck_pi1',
        ];
    }

    public function getTitle(): string
    {
        return 'ext:uw_a11y_check: Migrate plugins to content elements';
    }

    public function getDescription(): string
    {
        return 'Migrates existing plugin records and backend user permissions used by ext:uw_a11y_check.';
    }
}

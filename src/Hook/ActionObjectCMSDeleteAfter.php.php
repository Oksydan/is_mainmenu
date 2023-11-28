<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Hook;

use Oksydan\IsMainMenu\Handler\CMS\CMSDeleteHandler;
use PrestaShop\PrestaShop\Adapter\Validate;

class ActionObjectCMSDeleteAfter extends AbstractHook
{
    private CMSDeleteHandler $cmsDeleteHandler;

    public function __construct(
        \Is_mainmenu $module,
        \Context $context,
        CMSDeleteHandler $cmsDeleteHandler
    ) {
        parent::__construct($module, $context);

        $this->cmsDeleteHandler = $cmsDeleteHandler;
    }

    public function execute(array $params): void
    {
        $cms = $params['object'];

        if (!Validate::isLoadedObject($cms)) {
            return;
        }

        $this->cmsDeleteHandler->handle($cms);
    }
}

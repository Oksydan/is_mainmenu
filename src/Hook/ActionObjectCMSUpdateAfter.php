<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Hook;

use Oksydan\IsMainMenu\Handler\CMS\CMSUpdateHandler;
use PrestaShop\PrestaShop\Adapter\Validate;

class ActionObjectCMSUpdateAfter extends AbstractHook
{
    private CMSUpdateHandler $cmsUpdateHandler;

    public function __construct(
        \Is_mainmenu $module,
        \Context $context,
        CMSUpdateHandler $cmsUpdateHandler
    ) {
        parent::__construct($module, $context);

        $this->cmsUpdateHandler = $cmsUpdateHandler;
    }

    public function execute(array $params): void
    {
        $cms = $params['object'];

        xdebug_break();

        if (!Validate::isLoadedObject($cms)) {
            return;
        }

        $this->cmsUpdateHandler->handle($cms);
    }
}

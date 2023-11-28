<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Hook;

use Oksydan\IsMainMenu\Cache\FrontAjaxRequestCacheKey;

class ActionFrontControllerSetMedia extends AbstractHook
{
    private FrontAjaxRequestCacheKey $frontAjaxRequestCacheKey;

    public function __construct(
        \Is_mainmenu $module,
        \Context $context,
        FrontAjaxRequestCacheKey $frontAjaxRequestCacheKey
    ) {
        parent::__construct($module, $context);

        $this->frontAjaxRequestCacheKey = $frontAjaxRequestCacheKey;
    }

    public function execute(array $params): void
    {
        \Media::addJsDef([
            'getDesktopSubmenuAjaxUrl' => $this->context->link->getModuleLink($this->module->name, 'ajax', [
                'action' => 'getDesktopSubMenu',
                'ajax' => '1',
                'cache_key' => $this->frontAjaxRequestCacheKey->getContextBasedRequestCacheKey(),
            ]),
            'getMobileSubmenuAjaxUrl' => $this->context->link->getModuleLink($this->module->name, 'ajax', [
                'action' => 'getMobileSubMenu',
                'ajax' => '1',
                'cache_key' => $this->frontAjaxRequestCacheKey->getContextBasedRequestCacheKey(),
            ]),
        ]);
    }
}

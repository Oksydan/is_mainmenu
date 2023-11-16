<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Hook;

use Oksydan\IsMainMenu\Cache\TemplateCache;

class ActionFrontControllerSetMedia extends AbstractHook
{
    private TemplateCache $templateCache;

    public function __construct(
        \Is_mainmenu $module,
        \Context $context,
        TemplateCache $templateCache
    ) {
        parent::__construct($module, $context);

        $this->templateCache = $templateCache;
    }


    public function execute(array $params): void
    {
        \Media::addJsDef([
            'getDesktopSubmenuAjaxUrl' => $this->context->link->getModuleLink($this->module->name, 'ajax', [
                'action' => 'getDesktopSubMenu',
                'ajax' => '1',
                'cache_key' => $this->templateCache->getContextBasedRequestCacheKey(),
            ]),
            'getMobileSubmenuAjaxUrl' => $this->context->link->getModuleLink($this->module->name, 'ajax', [
                'action' => 'getMobileSubMenu',
                'ajax' => '1',
                'cache_key' => $this->templateCache->getContextBasedRequestCacheKey(),
            ]),
        ]);
    }
}

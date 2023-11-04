<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Hook;

use Oksydan\IsMainMenu\Cache\TemplateCache;
use Oksydan\IsMainMenu\Menu\MenuTree;

abstract class AbstractCacheableDisplayHook extends AbstractDisplayHook
{
    /**
     * @var TemplateCache
     */
    protected $templateCache;

    public function __construct(
        \Is_mainmenu $module,
        \Context $context,
        MenuTree $menuTree,
        TemplateCache $templateCache
    ) {
        parent::__construct($module, $context, $menuTree);

        $this->templateCache = $templateCache;
    }

    public function execute(array $params): string
    {
        if (!$this->shouldBlockBeDisplayed($params)) {
            return '';
        }

        if (!$this->isTemplateCached()) {
            $this->assignTemplateVariables($params);
        }

        return $this->module->fetch($this->getTemplateFullPath(), $this->getCacheKey());
    }

    protected function getCacheKey(): string
    {
        return $this->module->getCacheId();
    }

    protected function isTemplateCached(): bool
    {
        return $this->module->isCached($this->getTemplateFullPath(), $this->getCacheKey());
    }
}

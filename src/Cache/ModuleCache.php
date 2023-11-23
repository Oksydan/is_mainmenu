<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Cache;

class ModuleCache
{
    protected TemplateCache $templateCache;

    protected FrontAjaxRequestCacheKey $frontAjaxRequestCacheKey;

    public function __construct(
        TemplateCache $templateCache,
        FrontAjaxRequestCacheKey $frontAjaxRequestCacheKey
    ) {
        $this->templateCache = $templateCache;
        $this->frontAjaxRequestCacheKey = $frontAjaxRequestCacheKey;
    }

    public function clearCache(): void
    {
        $this->frontAjaxRequestCacheKey->setNewRequestCacheKey();
        $this->templateCache->clearTemplateCache();
    }
}

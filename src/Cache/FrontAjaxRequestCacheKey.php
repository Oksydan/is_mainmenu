<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Cache;

use PrestaShop\PrestaShop\Adapter\Configuration;

class FrontAjaxRequestCacheKey
{
    /*
     * @var cache key
     */
    protected const CACHE_KEY = 'IS_MAINMENU_REQUEST_CACHE_KEY';

    /*
     * @var Configuration
     */
    protected Configuration $configuration;

    /*
     * @var \Context
     */
    protected \Context $context;

    public function __construct(
        Configuration $configuration,
        \Context $context
    ) {
        $this->configuration = $configuration;
        $this->context = $context;
    }

    public function getContextBasedRequestCacheKey(): string
    {
        return md5($this->getRequestCacheKey() . '|' . $this->context->language->id . '|' . $this->context->shop->id . '|' . $this->context->customer->id_default_group);
    }

    private function getRequestCacheKey(): int
    {
        return $this->configuration->getInt(static::CACHE_KEY, 0);
    }

    public function setNewRequestCacheKey(): void
    {
        $current = $this->getRequestCacheKey() + 1;

        $this->configuration->set(static::CACHE_KEY, $current);
    }
}

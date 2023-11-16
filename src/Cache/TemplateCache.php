<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Cache;

use Is_mainmenu;
use Oksydan\IsImageslider\Hook\AbstractCacheableDisplayHook;
use Oksydan\IsMainMenu\LegacyRepository\ModuleHookLegacyRepository;
use PrestaShop\PrestaShop\Adapter\Configuration;

class TemplateCache
{
    /*
     * @var Is_mainmenu
     */
    protected \Is_mainmenu $module;

    /*
     * @var \Context
     */
    protected \Context $context;

    /*
     * @var ModuleHookLegacyRepository
     */
    protected ModuleHookLegacyRepository $hookModuleRepository;

    /*
     * @var Configuration
     */
    protected Configuration $configuration;

    protected const CACHE_KEY = 'IS_MAINMENU_REQUEST_CACHE_KEY';

    public function __construct(
        \Is_mainmenu $module,
        \Context $context,
        ModuleHookLegacyRepository $hookModuleRepository,
        Configuration $configuration
    ) {
        $this->module = $module;
        $this->context = $context;
        $this->hookModuleRepository = $hookModuleRepository;
        $this->configuration = $configuration;
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

    public function clearTemplateCache(): void
    {
        $hookedHooks = $this->hookModuleRepository->getAllHookRegisteredToModule($this->module->id);
        $uniqueHooks = [];

        foreach ($hookedHooks as $hook) {
            if (!in_array($hook['name'], $uniqueHooks)) {
                $uniqueHooks[] = $hook['name'];
            }
        }

        foreach ($uniqueHooks as $hook) {
            $this->clearCacheForHook($hook);
        }
    }

    private function clearCacheForHook($hookName): void
    {
        $displayHook = $this->getServiceFromHookName($hookName);

        if ($displayHook) {
            $this->module->_clearCache($displayHook->getTemplateFullPath());
        }
    }

    private function getServiceFromHookName($methodName): ?AbstractCacheableDisplayHook
    {
        $serviceName = sprintf(
            'Oksydan\IsMainMenu\Hook\%s',
            ucwords(str_replace('hook', '', $methodName))
        );

        $hook = $this->module->getService($serviceName);

        return $hook instanceof AbstractCacheableDisplayHook ? $hook : null;
    }
}

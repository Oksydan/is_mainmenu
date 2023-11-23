<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Cache;

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

    public function __construct(
        \Is_mainmenu $module,
        \Context $context,
        ModuleHookLegacyRepository $hookModuleRepository,
    ) {
        $this->module = $module;
        $this->context = $context;
        $this->hookModuleRepository = $hookModuleRepository;
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

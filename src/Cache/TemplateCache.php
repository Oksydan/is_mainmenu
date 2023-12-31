<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Cache;

use Oksydan\IsMainMenu\Hook\AbstractCacheableDisplayHook;
use Oksydan\IsMainMenu\LegacyRepository\ModuleHookLegacyRepository;
use Oksydan\IsMainMenu\View\Front\DesktopSubMenuRender;
use Oksydan\IsMainMenu\View\Front\MobileSubMenuRender;

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
     * @var DesktopSubMenuRender
     */
    protected DesktopSubMenuRender $desktopSubMenuRender;

    /*
     * @var MobileSubMenuRender
     */
    protected MobileSubMenuRender $mobileSubMenuRender;

    public function __construct(
        \Is_mainmenu $module,
        \Context $context,
        ModuleHookLegacyRepository $hookModuleRepository,
        DesktopSubMenuRender $desktopSubMenuRender,
        MobileSubMenuRender $mobileSubMenuRender
    ) {
        $this->module = $module;
        $this->context = $context;
        $this->hookModuleRepository = $hookModuleRepository;
        $this->desktopSubMenuRender = $desktopSubMenuRender;
        $this->mobileSubMenuRender = $mobileSubMenuRender;
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

        $this->desktopSubMenuRender->clearCache();
        $this->mobileSubMenuRender->clearCache();
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

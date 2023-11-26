<?php

namespace Oksydan\IsMainMenu\Handler\CMS;

use Oksydan\IsMainMenu\Cache\ModuleCache;
use Oksydan\IsMainMenu\Repository\MenuElementCmsRepository;

class CMSUpdateHandler implements CMSHandlerInterface
{
    private ModuleCache $moduleCache;

    private MenuElementCmsRepository $menuElementCmsRepository;

    public function __construct(
        ModuleCache $moduleCache,
        MenuElementCmsRepository $menuElementCmsRepository
    ) {
        $this->moduleCache = $moduleCache;
        $this->menuElementCmsRepository = $menuElementCmsRepository;
    }


    public function handle(\CMS $cms): void
    {
        $countMenuElementProduct = $this->menuElementCmsRepository->findCountMenuElementCmsByCmsId((int) $cms->id);

        if ($countMenuElementProduct > 0) {
            $this->moduleCache->clearCache();
        }
    }
}

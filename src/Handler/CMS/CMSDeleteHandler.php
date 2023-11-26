<?php

namespace Oksydan\IsMainMenu\Handler\CMS;

use Doctrine\ORM\EntityManagerInterface;
use Oksydan\IsMainMenu\Cache\ModuleCache;
use Oksydan\IsMainMenu\Entity\MenuElementCms;
use Oksydan\IsMainMenu\Repository\MenuElementCmsRepository;

class CMSDeleteHandler implements CMSHandlerInterface
{
    private ModuleCache $moduleCache;

    private MenuElementCmsRepository $menuElementCmsRepository;

    private EntityManagerInterface $entityManager;

    public function __construct(
        ModuleCache $moduleCache,
        MenuElementCmsRepository $menuElementCmsRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->moduleCache = $moduleCache;
        $this->menuElementCmsRepository = $menuElementCmsRepository;
        $this->entityManager = $entityManager;
    }


    public function handle(\CMS $cms): void
    {
        $cmsMenuElements = $this->menuElementCmsRepository->findMenuElementsCmsByCmsId((int) $cms->id);

        foreach ($cmsMenuElements as $cmsMenuElement) {
            $this->handleMenuElementCmsDelete($cmsMenuElement);
        }

        if (!empty($cmsMenuElements)) {
            $this->entityManager->flush();

            $this->moduleCache->clearCache();
        }
    }

    private function handleMenuElementCmsDelete(MenuElementCms $cmsMenuElement): void
    {
        $menuElement = $cmsMenuElement->getMenuElement();

        $menuElement->setActive(false);
        $this->entityManager->remove($cmsMenuElement);
    }
}

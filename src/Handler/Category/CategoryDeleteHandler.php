<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Handler\Category;

use Doctrine\ORM\EntityManagerInterface;
use Oksydan\IsMainMenu\Cache\ModuleCache;
use Oksydan\IsMainMenu\Entity\MenuElementCategory;
use Oksydan\IsMainMenu\Repository\MenuElementCategoryRepository;

class CategoryDeleteHandler implements CategoryHandlerInterface
{
    private ModuleCache $moduleCache;

    private MenuElementCategoryRepository $menuElementCategoryRepository;

    private EntityManagerInterface $entityManager;

    public function __construct(
        ModuleCache $moduleCache,
        MenuElementCategoryRepository $menuElementCategoryRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->moduleCache = $moduleCache;
        $this->menuElementCategoryRepository = $menuElementCategoryRepository;
        $this->entityManager = $entityManager;
    }

    public function handle(\Category $category): void
    {
        $categoryMenuElements = $this->menuElementCategoryRepository->findMenuElementsCategoryByCategoryId((int) $category->id);

        foreach ($categoryMenuElements as $categoryMenuElement) {
            $this->handleMenuElementCategoryDelete($categoryMenuElement);
        }

        if (!empty($categoryMenuElements)) {
            $this->entityManager->flush();

            $this->moduleCache->clearCache();
        }
    }

    private function handleMenuElementCategoryDelete(MenuElementCategory $menuElementCategory): void
    {
        $menuElement = $menuElementCategory->getMenuElement();

        $menuElement->setActive(false);
        $this->entityManager->remove($menuElementCategory);
    }
}

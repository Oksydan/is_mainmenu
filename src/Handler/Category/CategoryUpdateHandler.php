<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Handler\Category;

use Oksydan\IsMainMenu\Cache\ModuleCache;
use Oksydan\IsMainMenu\Repository\MenuElementCategoryRepository;

class CategoryUpdateHandler implements CategoryHandlerInterface
{
    private ModuleCache $moduleCache;

    private MenuElementCategoryRepository $menuElementCategoryRepository;

    public function __construct(
        ModuleCache $moduleCache,
        MenuElementCategoryRepository $menuElementCategoryRepository
    ) {
        $this->moduleCache = $moduleCache;
        $this->menuElementCategoryRepository = $menuElementCategoryRepository;
    }

    public function handle(\Category $category): void
    {
        $countMenuElementCategory = $this->menuElementCategoryRepository->findCountMenuElementCategoryByCategoryId((int) $category->id);

        if ($countMenuElementCategory > 0) {
            $this->moduleCache->clearCache();
        }
    }
}

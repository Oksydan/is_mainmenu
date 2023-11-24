<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Handler\Product;

use Oksydan\IsMainMenu\Cache\ModuleCache;
use Oksydan\IsMainMenu\Repository\MenuElementProductRepository;

class ProductUpdateHandler implements ProductHandlerInterface
{
    private ModuleCache $moduleCache;

    private MenuElementProductRepository $menuElementProductRepository;

    public function __construct(
        ModuleCache $moduleCache,
        MenuElementProductRepository $menuElementProductRepository
    ) {
        $this->moduleCache = $moduleCache;
        $this->menuElementProductRepository = $menuElementProductRepository;
    }

    public function handle(\Product $product): void
    {
        $countMenuElementProduct = $this->menuElementProductRepository->findCountMenuElementProductByProductId((int) $product->id);

        if ($countMenuElementProduct > 0) {
            $this->moduleCache->clearCache();
        }
    }
}

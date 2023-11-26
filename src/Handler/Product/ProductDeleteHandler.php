<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Handler\Product;

use Doctrine\ORM\EntityManagerInterface;
use Oksydan\IsMainMenu\Cache\ModuleCache;
use Oksydan\IsMainMenu\Entity\MenuElementProduct;
use Oksydan\IsMainMenu\Repository\MenuElementProductRepository;

class ProductDeleteHandler implements ProductHandlerInterface
{
    private ModuleCache $moduleCache;

    private MenuElementProductRepository $menuElementProductRepository;

    private EntityManagerInterface $entityManager;

    public function __construct(
        ModuleCache $moduleCache,
        MenuElementProductRepository $menuElementProductRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->moduleCache = $moduleCache;
        $this->menuElementProductRepository = $menuElementProductRepository;
        $this->entityManager = $entityManager;
    }

    public function handle(\Product $product): void
    {
        $productMenuElements = $this->menuElementProductRepository->findMenuElementsProductByProductId((int) $product->id);

        foreach ($productMenuElements as $productMenuElement) {
            $this->handleMenuElementProductDelete($productMenuElement);
        }

        if (!empty($productMenuElements)) {
            $this->entityManager->flush();

            $this->moduleCache->clearCache();
        }
    }

    private function handleMenuElementProductDelete(MenuElementProduct $productMenuElement): void
    {
        $menuElement = $productMenuElement->getMenuElement();

        $menuElement->setActive(false);
        $this->entityManager->remove($productMenuElement);
    }
}

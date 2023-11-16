<?php

namespace Oksydan\IsMainMenu\Menu;

use Oksydan\IsMainMenu\Entity\MenuElementCategory;
use Oksydan\IsMainMenu\Entity\MenuElementCms;
use Oksydan\IsMainMenu\Entity\MenuElementProduct;
use Oksydan\IsMainMenu\Entity\MenuElementRelatedEntityInterface;
use Oksydan\IsMainMenu\LegacyRepository\CategoryLegacyRepository;
use Oksydan\IsMainMenu\LegacyRepository\CmsLegacyRepository;
use Oksydan\IsMainMenu\LegacyRepository\ProductLegacyRepository;

class MenuElementVisibilityManager
{
    /**
     * @var \Context
     */
    private \Context $context;

    /**
     * @var CategoryLegacyRepository
     */
    private CategoryLegacyRepository $categoryLegacyRepository;

    /**
     * @var CmsLegacyRepository
     */
    private CmsLegacyRepository $cmsLegacyRepository;

    /**
     * @var ProductLegacyRepository
     */
    private ProductLegacyRepository $productLegacyRepository;

    public function __construct(
        \Context $context,
        CategoryLegacyRepository $categoryLegacyRepository,
        CmsLegacyRepository $cmsLegacyRepository,
        ProductLegacyRepository $productLegacyRepository
    ) {
        $this->context = $context;
        $this->categoryLegacyRepository = $categoryLegacyRepository;
        $this->cmsLegacyRepository = $cmsLegacyRepository;
        $this->productLegacyRepository = $productLegacyRepository;
    }

    public function shouldBeElementDisplayed(?MenuElementRelatedEntityInterface $menuElement): bool
    {
        switch (get_class($menuElement)) {
            case MenuElementCategory::class:
                return $this->categoryLegacyRepository->isCategoryActiveAndVisible(
                    $menuElement->getIdCategory(),
                    $this->context->shop->id,
                    $this->context->customer->id_default_group
                );
            case MenuElementCms::class:
                return $this->cmsLegacyRepository->isCmsPageAciveForStore(
                    $menuElement->getIdCms(),
                    $this->context->shop->id
                );
            case MenuElementProduct::class:
                return $this->productLegacyRepository->isProductActiveForStoreAndVisible(
                    $menuElement->getIdProduct(),
                    $this->context->shop->id
                );
        }

        return true;
    }
}

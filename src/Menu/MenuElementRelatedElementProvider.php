<?php

namespace Oksydan\IsMainMenu\Menu;

use Oksydan\IsMainMenu\Entity\MenuElement;
use Oksydan\IsMainMenu\Entity\MenuElementRelatedEntityInterface;
use Oksydan\IsMainMenu\Repository\MenuElementBannerRepository;
use Oksydan\IsMainMenu\Repository\MenuElementCategoryRepository;
use Oksydan\IsMainMenu\Repository\MenuElementCmsRepository;
use Oksydan\IsMainMenu\Repository\MenuElementCustomRepository;
use Oksydan\IsMainMenu\Repository\MenuElementHtmlRepository;
use Oksydan\IsMainMenu\Repository\MenuElementProductRepository;

class MenuElementRelatedElementProvider
{
    /**
     * @var MenuElementHtmlRepository
     */
    private MenuElementHtmlRepository $menuElementHtmlRepository;

    /**
     * @var MenuElementBannerRepository
     */
    private MenuElementBannerRepository $menuElementBannerRepository;

    /**
     * @var MenuElementCategoryRepository
     */
    private MenuElementCategoryRepository $menuElementCategoryRepository;

    /**
     * @var MenuElementCmsRepository
     */
    private MenuElementCmsRepository $menuElementCmsRepository;

    /**
     * @var MenuElementCustomRepository
     */
    private MenuElementCustomRepository $menuElementCustomRepository;

    /**
     * @var MenuElementProductRepository
     */
    private MenuElementProductRepository $menuElementProductRepository;

    public function __construct(
        MenuElementHtmlRepository $menuElementHtmlRepository,
        MenuElementBannerRepository $menuElementBannerRepository,
        MenuElementCategoryRepository $menuElementCategoryRepository,
        MenuElementCustomRepository $menuElementCustomRepository,
        MenuElementCmsRepository $menuElementCmsRepository,
        MenuElementProductRepository $menuElementProductRepository
    )
    {
        $this->menuElementHtmlRepository = $menuElementHtmlRepository;
        $this->menuElementBannerRepository = $menuElementBannerRepository;
        $this->menuElementCategoryRepository = $menuElementCategoryRepository;
        $this->menuElementCustomRepository = $menuElementCustomRepository;
        $this->menuElementCmsRepository = $menuElementCmsRepository;
        $this->menuElementProductRepository = $menuElementProductRepository;
    }

    public function getRelatedMenuElementByMenuElement(MenuElement $menuElement): ?MenuElementRelatedEntityInterface
    {
        switch ($menuElement->getType()) {
            case MenuElement::TYPE_HTML:
                return $this->menuElementHtmlRepository->findMenuElementHtmlByMenuElement($menuElement);
            case MenuElement::TYPE_BANNER:
                return $this->menuElementBannerRepository->findMenuElementBannerByMenuElement($menuElement);
            case MenuElement::TYPE_LINK:
                return $this->menuElementCustomRepository->findMenuElementCustomByMenuElement($menuElement);
            case MenuElement::TYPE_CATEGORY:
                return $this->menuElementCategoryRepository->findMenuElementCategoryByMenuElement($menuElement);
            case MenuElement::TYPE_CMS:
                return $this->menuElementCmsRepository->findMenuElementCmsByMenuElement($menuElement);
            case MenuElement::TYPE_PRODUCT:
                return $this->menuElementProductRepository->findMenuElementProductByMenuElement($menuElement);
        }

        return null;
    }
}

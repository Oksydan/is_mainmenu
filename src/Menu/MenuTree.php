<?php

namespace Oksydan\IsMainMenu\Menu;

use Oksydan\IsMainMenu\Entity\MenuElement;
use Oksydan\IsMainMenu\Presenter\MenuElementPresenter;
use Oksydan\IsMainMenu\Repository\MenuElementBannerRepository;
use Oksydan\IsMainMenu\Repository\MenuElementCategoryRepository;
use Oksydan\IsMainMenu\Repository\MenuElementCustomRepository;
use Oksydan\IsMainMenu\Repository\MenuElementHtmlRepository;
use Oksydan\IsMainMenu\Repository\MenuElementRepository;

class MenuTree
{
    /**
     * @var MenuElementRepository
     */
    private MenuElementRepository $menuElementRepository;

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
     * @var MenuElementCustomRepository
     */
    private MenuElementCustomRepository $menuElementCustomRepository;

    /*
     * @var MenuElementPresenter
     */
    private MenuElementPresenter $menuElementPresenter;

    /**
     * @var \Context
     */
    private \Context $context;

    public function __construct(
        MenuElementRepository $menuElementRepository,
        MenuElementHtmlRepository $menuElementHtmlRepository,
        MenuElementBannerRepository $menuElementBannerRepository,
        MenuElementCategoryRepository $menuElementCategoryRepository,
        MenuElementCustomRepository $menuElementCustomRepository,
        MenuElementPresenter $menuElementPresenter,
        \Context $context
    ) {
        $this->menuElementRepository = $menuElementRepository;
        $this->menuElementHtmlRepository = $menuElementHtmlRepository;
        $this->menuElementBannerRepository = $menuElementBannerRepository;
        $this->menuElementCategoryRepository = $menuElementCategoryRepository;
        $this->menuElementCustomRepository = $menuElementCustomRepository;
        $this->menuElementPresenter = $menuElementPresenter;
        $this->context = $context;
    }

    public function getMenuTree(): array
    {
        $root = $this->menuElementRepository->getRootElement();

        return $this->buildMenuTreeRecursively($root);
    }

    private function buildMenuTreeRecursively(MenuElement $menuElement): array
    {
        $children = $this->getElementChildren($menuElement);
        $tree = [];

        foreach ($children as $child) {
            $relatedMenuElement = $this->getRelatedMenuElementByMenuElement($child);

            if ($relatedMenuElement) {
                $tree[] = [
                    ...$this->menuElementPresenter->present($child, $relatedMenuElement),
                    'children' => $this->buildMenuTreeRecursively($child),
                ];
            }
        }

        return $tree;
    }

    private function getElementChildren(MenuElement $menuElement): array
    {
        return $this->menuElementRepository->getActiveMenuElementChildrenByStoreId($menuElement, $this->context->shop->id);
    }

    private function getRelatedMenuElementByMenuElement(MenuElement $menuElement)
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
        }

        return null;
    }
}

<?php

namespace Oksydan\IsMainMenu\Menu;

use Oksydan\IsMainMenu\Entity\MenuElement;
use Oksydan\IsMainMenu\Presenter\Menu\MenuElementPresenter;
use Oksydan\IsMainMenu\Repository\MenuElementRepository;

class MenuTree
{
    public const MENU_TYPE_MOBILE = 'mobile';
    public const MENU_TYPE_DESKTOP = 'desktop';

    /**
     * @var MenuElementRepository
     */
    private MenuElementRepository $menuElementRepository;

    /*
     * @var MenuElementPresenter
     */
    private MenuElementPresenter $menuElementPresenter;

    /**
     * @var MenuElementVisibilityManager
     */
    private MenuElementVisibilityManager $menuElementVisibilityManager;

    /**
     * @var MenuElementRelatedElementProvider
     */
    private MenuElementRelatedElementProvider $menuElementRelatedElementProvider;

    /**
     * @var \Context
     */
    private \Context $context;

    public function __construct(
        MenuElementRepository $menuElementRepository,
        MenuElementPresenter $menuElementPresenter,
        MenuElementVisibilityManager $menuElementVisibilityManager,
        MenuElementRelatedElementProvider $menuElementRelatedElementProvider,
        \Context $context
    ) {
        $this->menuElementRepository = $menuElementRepository;
        $this->menuElementPresenter = $menuElementPresenter;
        $this->menuElementVisibilityManager = $menuElementVisibilityManager;
        $this->menuElementRelatedElementProvider = $menuElementRelatedElementProvider;
        $this->context = $context;
    }

    public function getMenuTree(int $idElement = null, string $menuType = self::MENU_TYPE_DESKTOP, int $maxDepth = null): array
    {
        if ($idElement) {
            $root = $this->menuElementRepository->getMenuElementById($idElement);
        } else {
            $root = $this->menuElementRepository->getRootElement();
        }

        return $this->buildMenuTreeRecursively($root, $menuType, 0, $maxDepth);
    }

    private function buildMenuTreeRecursively(MenuElement $menuElement, string $menuType, int $currentDepth, ?int $maxDepth): array
    {
        $children = $this->getElementChildren($menuElement);
        $tree = [];
        ++$currentDepth;

        if ($maxDepth && $currentDepth > $maxDepth) {
            return $tree;
        }

        foreach ($children as $child) {
            $relatedMenuElement = $this->menuElementRelatedElementProvider->getRelatedMenuElementByMenuElement($child);

            if ($relatedMenuElement && $this->menuElementVisibilityManager->shouldBeElementDisplayed($child, $relatedMenuElement, $menuType)) {
                $tree[] = [
                    ...$this->menuElementPresenter->present($child, $relatedMenuElement, $menuType),
                    'children' => $this->buildMenuTreeRecursively($child, $menuType, $currentDepth, $maxDepth),
                ];
            }
        }

        return $tree;
    }

    private function getElementChildren(MenuElement $menuElement): array
    {
        return $this->menuElementRepository->getActiveMenuElementChildrenByStoreId($menuElement, $this->context->shop->id);
    }
}

<?php

namespace Oksydan\IsMainMenu\View\Front;

use Oksydan\IsMainMenu\Entity\MenuElement;
use Oksydan\IsMainMenu\Menu\MenuElementRelatedElementProvider;
use Oksydan\IsMainMenu\Menu\MenuTree;
use Oksydan\IsMainMenu\Presenter\Menu\MenuElementPresenter;
use Oksydan\IsMainMenu\Repository\MenuElementRepository;

class MobileSubMenuRender extends AbstractMenuRender implements MenuFrontRenderInterface
{
    protected string $templateFile = 'mobile-submenu.tpl';

    private MenuTree $menuTree;

    private MenuElementRepository $menuElementRepository;

    public function __construct(
        \Is_mainmenu $module,
        \Context $context,
        MenuTree $menuTree,
        MenuElementRepository $menuElementRepository,
        MenuElementRelatedElementProvider $menuElementRelatedElementProvider,
        MenuElementPresenter $menuElementPresenter
    ) {
        parent::__construct($module, $context);
        $this->menuTree = $menuTree;
        $this->menuElementRepository = $menuElementRepository;
        $this->menuElementRelatedElementProvider = $menuElementRelatedElementProvider;
        $this->menuElementPresenter = $menuElementPresenter;
    }

    protected function getCacheKey($idMenuElement): string
    {
        return $this->module->getCacheId('mobile_submenu_' . $idMenuElement);
    }

    protected function assignTemplateVariables($idMenuElement): void
    {
        $parentElement = $this->menuElementRepository->find($idMenuElement);
        $parentPresented = $this->presentParentElement($parentElement);

        $this->context->smarty->assign([
            'menu' => $this->menuTree->getMenuTree($idMenuElement, MenuTree::MENU_TYPE_MOBILE, 1),
            'depth' => $parentElement->getDepth() + 1,
            'parent' => $parentPresented,
        ]);
    }

    protected function presentParentElement(MenuElement $menuElement)
    {
        $relatedElement = $this->menuElementRelatedElementProvider->getRelatedMenuElementByMenuElement($menuElement);

        return $this->menuElementPresenter->present($menuElement, $relatedElement);
    }
}

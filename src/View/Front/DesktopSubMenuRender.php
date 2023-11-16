<?php

namespace Oksydan\IsMainMenu\View\Front;

use Oksydan\IsMainMenu\Menu\MenuTree;

class DesktopSubMenuRender extends AbstractMenuRender implements MenuFrontRenderInterface
{
    protected string $templateFile = 'desktopSubmenu.tpl';

    private MenuTree $menuTree;

    public function __construct(
        \Is_mainmenu $module,
        \Context $context,
        MenuTree $menuTree
    )
    {
        parent::__construct($module, $context);
        $this->menuTree = $menuTree;
    }

    protected function getCacheKey($idMenuElement): string
    {
        return $this->module->getCacheId('desktop_submenu_' . $idMenuElement);
    }

    protected function assignTemplateVariables($idMenuElement): void
    {
        $this->context->smarty->assign([
            'menu_tree' => $this->menuTree->getMenuTree($idMenuElement),
        ]);
    }
}

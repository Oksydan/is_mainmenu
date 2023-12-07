<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Hook;

use Oksydan\IsMainMenu\Menu\MenuTree;

class DisplayMobileMenu extends AbstractCacheableDisplayHook
{
    protected function assignTemplateVariables(array $params)
    {
        $menuElements = $this->menuTree->getMenuTree(null, MenuTree::MENU_TYPE_MOBILE, 1);

        $this->context->smarty->assign([
            'menu' => $menuElements,
        ]);
    }

    protected function getTemplate(): string
    {
        return 'displayMobileMenu.tpl';
    }
}

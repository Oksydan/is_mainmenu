<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Hook;

class DisplayTop extends AbstractCacheableDisplayHook
{
    protected function assignTemplateVariables(array $params)
    {
        $menuElements = $this->menuTree->getMenuTree(null, 1);

        $this->context->smarty->assign([
            'menu' => $menuElements,
        ]);
    }

    protected function getTemplate(): string
    {
        return 'displayTop.tpl';
    }
}

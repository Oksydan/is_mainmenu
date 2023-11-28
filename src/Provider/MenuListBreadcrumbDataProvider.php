<?php

namespace Oksydan\IsMainMenu\Provider;

use Oksydan\IsMainMenu\Entity\MenuElement;

class MenuListBreadcrumbDataProvider implements MenuListBreadcrumbDataProviderInterface
{
    public function provide(MenuElement $menuElement): array
    {
        $breadcrumb = [];
        $breadcrumb[] = $menuElement;

        while ($parentMenuElement = $this->getParentElement($menuElement)) {
            $breadcrumb[] = $parentMenuElement;
            $menuElement = $parentMenuElement;
        }

        return array_reverse($breadcrumb);
    }

    private function getParentElement(MenuElement $menuElement): ?MenuElement
    {
        return $menuElement->getParentMenuElement();
    }
}

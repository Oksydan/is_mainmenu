<?php

namespace Oksydan\IsMainMenu\Provider;

use Oksydan\IsMainMenu\Entity\MenuElement;

interface MenuListBreadcrumbDataProviderInterface
{
    public function provide(MenuElement $menuElement): array;
}

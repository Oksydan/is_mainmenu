<?php

namespace Oksydan\IsMainMenu\Presenter\Menu;

use Oksydan\IsMainMenu\Entity\MenuElement;
use Oksydan\IsMainMenu\Entity\MenuElementRelatedEntityInterface;

interface MenuElementPresenterInterface
{
    public function present(
        MenuElement $menuElement,
        MenuElementRelatedEntityInterface $relatedMenuElement,
        string $menuType
    ): array;
}

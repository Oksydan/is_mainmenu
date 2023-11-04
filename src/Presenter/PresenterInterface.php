<?php

namespace Oksydan\IsMainMenu\Presenter;

use Oksydan\IsMainMenu\Entity\MenuElement;
use Oksydan\IsMainMenu\Entity\MenuElementRelatedEntityInterface;

interface PresenterInterface
{
    public function present(MenuElement $menuElement, MenuElementRelatedEntityInterface $relatedMenuElement): array;
}

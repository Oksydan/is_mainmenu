<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Repository;

use Doctrine\ORM\EntityRepository;
use Oksydan\IsMainMenu\Entity\MenuElement;
use Oksydan\IsMainMenu\Entity\MenuElementCategory;

class MenuElementCategoryRepository extends EntityRepository
{
    public function findMenuElementCategoryByMenuElement(MenuElement $menuElement): ?MenuElementCategory
    {
        return $this->findOneBy(['menuElement' => $menuElement]);
    }
}

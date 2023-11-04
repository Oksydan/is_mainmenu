<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Repository;

use Doctrine\ORM\EntityRepository;
use Oksydan\IsMainMenu\Entity\MenuElement;
use Oksydan\IsMainMenu\Entity\MenuElementCustom;

class MenuElementCustomRepository extends EntityRepository
{
    public function findMenuElementCustomByMenuElement(MenuElement $menuElement): ?MenuElementCustom
    {
        return $this->findOneBy(['menuElement' => $menuElement]);
    }
}

<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Repository;

use Doctrine\ORM\EntityRepository;
use Oksydan\IsMainMenu\Entity\MenuElement;
use Oksydan\IsMainMenu\Entity\MenuElementBanner;

class MenuElementBannerRepository extends EntityRepository
{
    public function findMenuElementBannerByMenuElement(MenuElement $menuElement): ?MenuElementBanner
    {
        return $this->findOneBy(['menuElement' => $menuElement]);
    }
}

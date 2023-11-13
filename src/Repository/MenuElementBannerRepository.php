<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Oksydan\IsMainMenu\Entity\MenuElement;
use Oksydan\IsMainMenu\Entity\MenuElementBanner;

class MenuElementBannerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MenuElementBanner::class);
    }

    public function findMenuElementBannerByMenuElement(MenuElement $menuElement): ?MenuElementBanner
    {
        return $this->findOneBy(['menuElement' => $menuElement]);
    }
}

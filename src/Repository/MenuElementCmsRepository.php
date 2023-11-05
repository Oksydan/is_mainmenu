<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Repository;

use Oksydan\IsMainMenu\Entity\MenuElement;
use Oksydan\IsMainMenu\Entity\MenuElementCms;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class MenuElementCmsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MenuElementCms::class);
    }

    public function findMenuElementCmsByMenuElement(MenuElement $menuElement): ?MenuElementCms
    {
        return $this->findOneBy(['menuElement' => $menuElement]);
    }
}

<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Repository;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Oksydan\IsMainMenu\Entity\MenuElement;
use Oksydan\IsMainMenu\Entity\MenuElementCustom;

class MenuElementCustomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MenuElementCustom::class);
    }

    public function findMenuElementCustomByMenuElement(MenuElement $menuElement): ?MenuElementCustom
    {
        return $this->findOneBy(['menuElement' => $menuElement]);
    }
}

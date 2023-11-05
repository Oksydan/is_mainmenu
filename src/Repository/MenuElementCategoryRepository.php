<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Oksydan\IsMainMenu\Entity\MenuElement;
use Oksydan\IsMainMenu\Entity\MenuElementCategory;

class MenuElementCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MenuElementCategory::class);
    }

    public function findMenuElementCategoryByMenuElement(MenuElement $menuElement): ?MenuElementCategory
    {
        return $this->findOneBy(['menuElement' => $menuElement]);
    }
}

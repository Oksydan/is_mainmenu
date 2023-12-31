<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Oksydan\IsMainMenu\Entity\MenuElement;
use Oksydan\IsMainMenu\Entity\MenuElementCms;

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

    public function findCountMenuElementCmsByCmsId(int $cmsId): int
    {
        return $this->count(['idCMS' => $cmsId]);
    }

    /**
     * @return MenuElementCms[]
     */
    public function findMenuElementsCmsByCmsId(int $cmsId): array
    {
        return $this->findBy(['idCMS' => $cmsId]);
    }
}

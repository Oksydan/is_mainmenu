<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Oksydan\IsMainMenu\Entity\MenuElement;
use Oksydan\IsMainMenu\Entity\MenuElementHtml;

class MenuElementHtmlRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MenuElementHtml::class);
    }

    public function findMenuElementHtmlByMenuElement(MenuElement $menuElement): ?MenuElementHtml
    {
        return $this->findOneBy(['menuElement' => $menuElement]);
    }
}

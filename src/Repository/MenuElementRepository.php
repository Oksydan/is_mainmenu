<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Repository;

use Doctrine\ORM\EntityRepository;
use Oksydan\IsMainMenu\Entity\MenuElement;

class MenuElementRepository extends EntityRepository
{
    public function getHighestPosition(MenuElement $parentMenuElement): int
    {
        $position = 0;
        $qb = $this
            ->createQueryBuilder('menu_element')
            ->select('menu_element.position')
            ->orderBy('menu_element.position', 'DESC')
            ->where('menu_element.parentMenuElement = :parentMenuElement')
            ->setParameter('parentMenuElement', $parentMenuElement)
            ->setMaxResults(1)
            ->getQuery();

        $result = $qb->getOneOrNullResult();

        if ($result) {
            $position = $result['position'];
        }

        return $position;
    }

    public function getRootElement(): ?MenuElement
    {
        return $this->findOneBy(['type' => MenuElement::TYPE_ROOT]);
    }

    public function findElementChildren(MenuElement $menuElement): array
    {
        $qb = $this
            ->createQueryBuilder('menu_element')
            ->select('menu_element')
            ->where('menu_element.parentMenuElement = :parentMenuElement')
            ->setParameter('parentMenuElement', $menuElement)
            ->getQuery();

        $result = $qb->getResult();

        return $result ?? [];
    }

    public function getActiveMenuElementChildrenByStoreId(MenuElement $menuElement, int $storeId): array
    {
        $qb = $this
            ->createQueryBuilder('menu_element')
            ->select('menu_element')
            ->andWhere('menu_element.parentMenuElement = :parentMenuElement')
            ->andWhere('menu_element.active = :active')
            ->andWhere(':storeId MEMBER OF menu_element.shops')
            ->setParameter('parentMenuElement', $menuElement)
            ->setParameter('active', true)
            ->setParameter('storeId', $storeId)
            ->orderBy('menu_element.position', 'ASC')
            ->getQuery();

        $result = $qb->getResult();

        return $result ?? [];
    }
}

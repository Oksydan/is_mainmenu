<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Oksydan\IsMainMenu\Entity\MenuElement;
use Oksydan\IsMainMenu\Entity\MenuElementProduct;

class MenuElementProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MenuElementProduct::class);
    }

    public function findMenuElementProductByMenuElement(MenuElement $menuElement): ?MenuElementProduct
    {
        return $this->findOneBy(['menuElement' => $menuElement]);
    }

    public function findCountMenuElementProductByProductId(int $idProduct): int
    {
        return $this->count(['id_product' => $idProduct]);
    }

    /**
     * @return MenuElementProduct[]
     */
    public function findMenuElementsProductByProductId(int $idProduct): array
    {
        return $this->findBy(['id_product' => $idProduct]);
    }
}

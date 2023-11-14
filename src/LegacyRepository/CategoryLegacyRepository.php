<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\LegacyRepository;

use Doctrine\DBAL\Connection;

/**
 * Class HookModuleRepository is responsible for retrieving module data from database.
 */
class CategoryLegacyRepository
{
    /**
     * @var Connection
     */
    private Connection $connection;

    /**
     * @var string
     */
    private string $dbPrefix;

    /**
     * @var string
     */
    private string $table;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     */
    public function __construct(Connection $connection, string $dbPrefix)
    {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->table = $this->dbPrefix . 'category';
    }

    public function isCategoryActiveAndVisible(int $idCategory, int $idStore, int $idGroup): bool
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('c.id_category')
            ->from($this->table, 'c')
            ->leftJoin('c', $this->dbPrefix . 'category_shop', 'cs', 'cs.id_category = c.id_category')
            ->leftJoin('cs', $this->dbPrefix . 'category_group', 'cg', 'cg.id_category = c.id_category')
            ->andWhere('c.id_category = :id_category')
            ->andWhere('cs.id_shop = :id_shop')
            ->andWhere('cg.id_group = :id_group')
            ->andWhere('c.active = 1')
            ->setParameter('id_category', $idCategory)
            ->setParameter('id_shop', $idStore)
            ->setParameter('id_group', $idGroup);

        return !empty($qb->execute()->fetchAllAssociative());
    }

    public function getCategoryChildren(int $idCategory): array
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('c.id_category')
            ->from($this->table, 'c')
            ->andWhere('c.id_parent = :id_parent')
            ->setParameter('id_parent', $idCategory);

        return $qb->execute()->fetchAllAssociative() ?? [];
    }

    public function getEnabledStoresForCategory(int $idCategory): array
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('cs.id_shop')
            ->from($this->dbPrefix . 'shop', 'cs')
            ->andWhere('cs.id_category = :id_category')
            ->setParameter('id_category', $idCategory);

        return $qb->execute()->fetchAllAssociative() ?? [];
    }

    /**
     * @param int $idCategory
     *
     * @return array ['id_lang' => 'name']
     *
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getCategoryLangArray(int $idCategory): array
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('cl.name, cl.id_lang')
            ->from($this->dbPrefix . 'category_lang', 'cl')
            ->andWhere('cl.id_category = :id_category')
            ->setParameter('id_category', $idCategory);

        $result = $qb->execute()->fetchAllAssociative();
        $categoriesByLangId = [];

        if (!empty($result)) {
            foreach ($result as $category) {
                $categoriesByLangId[$category['id_lang']] = $category['name'];
            }
        }

        return $categoriesByLangId;
    }
}

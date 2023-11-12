<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\LegacyRepository;

use Doctrine\DBAL\Connection;

/**
 * Class HookModuleRepository is responsible for retrieving module data from database.
 */
class CmsLegacyRepository
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
        $this->table = $this->dbPrefix . 'cms';
    }


    public function isCmsPageAciveForStore(int $idCategory, int $idStore): bool
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('c.id_cms')
            ->from($this->table, 'c')
            ->leftJoin('c', $this->dbPrefix . 'cms_shop', 'cs', 'cs.id_cms = c.id_cms')
            ->andWhere('c.id_cms = :id_cms')
            ->andWhere('cs.id_shop = :id_shop')
            ->andWhere('c.active = 1')
            ->setParameter('id_cms', $idCategory)
            ->setParameter('id_shop', $idStore);

        return !empty($qb->execute()->fetchAllAssociative());
    }

}

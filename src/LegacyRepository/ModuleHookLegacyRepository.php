<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\LegacyRepository;

use Doctrine\DBAL\Connection;

class ModuleHookLegacyRepository
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
    public function __construct(
        Connection $connection,
        string $dbPrefix
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->table = $this->dbPrefix . 'hook_module';
    }

    public function getAllHookRegisteredToModule(int $moduleId): array
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('h.name')
            ->from($this->table, 'mh')
            ->where('mh.id_module = :id_module')
            ->leftJoin('mh', $this->dbPrefix . 'hook', 'h', 'h.id_hook = mh.id_hook')
            ->setParameter('id_module', $moduleId);

        try {
            $response = $qb->execute()->fetchAllAssociative();
        } catch (\Exception $e) {
            $response = [];
        }

        return $response ?? [];
    }
}

<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\LegacyRepository;

use Doctrine\DBAL\Connection;

/**
 * Class HookModuleRepository is responsible for retrieving module data from database.
 */
class ProductLegacyRepository
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
        $this->table = $this->dbPrefix . 'product';
    }

    public function getProductsByQuery(
        string $query,
        int $idLang,
        int $limit = 40
    ): array {
        $qb = $this->connection->createQueryBuilder()
            ->select('p.id_product, pa.id_product_attribute, pl.name, p.reference, pa.reference AS attribute_reference, pl.link_rewrite')
            ->from($this->table, 'p')
            ->leftJoin('p', $this->dbPrefix . 'product_lang', 'pl', 'pl.id_product = p.id_product')
            ->leftJoin('p', $this->dbPrefix . 'product_attribute', 'pa', 'pa.id_product = p.id_product')
            ->andWhere('pl.id_lang = :id_lang')
            ->andWhere('pl.name LIKE :query OR p.reference LIKE :query OR pa.reference LIKE :query')
            ->setParameter('id_lang', $idLang)
            ->setParameter('query', '%' . $query . '%')
            ->setMaxResults($limit);

        return $qb->execute()->fetchAllAssociative();
    }

    public function getProductDataByIdAndIdAttribute(
        int $idProduct,
        int $idProductAttribute,
        int $idLang
    ): array {
        $qb = $this->connection->createQueryBuilder()
            ->select('p.id_product, pa.id_product_attribute, pl.name, p.reference, pa.reference AS attribute_reference, pl.link_rewrite')
            ->from($this->table, 'p')
            ->leftJoin('p', $this->dbPrefix . 'product_lang', 'pl', 'pl.id_product = p.id_product')
            ->leftJoin('p', $this->dbPrefix . 'product_attribute', 'pa', 'pa.id_product = p.id_product')
            ->andWhere('pl.id_lang = :id_lang')
            ->andWhere('p.id_product = :id_product')
            ->setParameter('id_product', $idProduct)
            ->setParameter('id_lang', $idLang);

        if ($idProductAttribute > 0) {
            $qb->andWhere('pa.id_product_attribute = :id_product_attribute')
                ->setParameter('id_product_attribute', $idProductAttribute);
        }

        $result = $qb->execute()->fetchAllAssociative();

        return $result[0] ?? [];
    }

    public function getProductCombinationForIdProductAttribute(
        int $idProduct,
        int $idProductAttribute,
        int $idLang
    ): array {
        $qb = $this->connection->createQueryBuilder()
            ->select('alg.name AS group_name, al.`name` AS attribute_name')
            ->from($this->dbPrefix . 'product_attribute', 'pa')
            ->leftJoin('pa', $this->dbPrefix . 'product_attribute_combination', 'pac', 'pac.id_product_attribute = pa.id_product_attribute')
            ->leftJoin('pac', $this->dbPrefix . 'attribute', 'a', 'a.id_attribute = pac.id_attribute')
            ->leftJoin('a', $this->dbPrefix . 'attribute_lang', 'al', 'a.id_attribute = al.id_attribute')
            ->leftJoin('a', $this->dbPrefix . 'attribute_group', 'ag', 'ag.id_attribute_group = a.id_attribute_group')
            ->leftJoin('ag', $this->dbPrefix . 'attribute_group_lang', 'alg', 'ag.id_attribute_group = alg.id_attribute_group')
            ->where('pa.id_product = :id_product')
            ->andWhere('pa.id_product_attribute = :id_product_attribute')
            ->andWhere('al.id_lang = :id_lang')
            ->setParameter('id_product', $idProduct)
            ->setParameter('id_product_attribute', $idProductAttribute)
            ->setParameter('id_lang', $idLang)
            ->groupBy('pa.id_product_attribute, ag.id_attribute_group')
            ->orderBy('pa.id_product_attribute');

        return $qb->execute()->fetchAllAssociative();
    }


    public function getProductCombinationImagesForIdProductAttribute(
        int $idProduct,
        int $idProductAttribute
    ): array {
        $qb = $this->connection->createQueryBuilder()
            ->select('i.id_image')
            ->from($this->dbPrefix . 'product_attribute', 'pa')
            ->leftJoin('pa', $this->dbPrefix . 'product_attribute_image', 'pai', 'pai.id_product_attribute = pa.id_product_attribute')
            ->leftJoin('pai', $this->dbPrefix . 'image', 'i', 'i.id_image = pai.id_image')
            ->where('pa.id_product = :id_product')
            ->andWhere('pa.id_product_attribute = :id_product_attribute')
            ->setParameter('id_product', $idProduct)
            ->setParameter('id_product_attribute', $idProductAttribute);

        return $qb->execute()->fetchAllAssociative();
    }

    public function getProductCoverForIdProduct(
        int $idProduct
    ): int {
        $qb = $this->connection->createQueryBuilder()
            ->select('i.id_image')
            ->from($this->dbPrefix . 'image', 'i')
            ->where('i.id_product = :id_product')
            ->andWhere('i.cover = 1')
            ->setParameter('id_product', $idProduct);

        $result = $qb->execute()->fetchAllAssociative();

        return $result[0]['id_image'] ?? 0;
    }

    public function isProductActiveForStoreAndVisible($idProduct, $idStore): bool
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('p.id_product')
            ->from($this->table, 'p')
            ->leftJoin('p', $this->dbPrefix . 'product_shop', 'ps', 'ps.id_product = p.id_product')
            ->andWhere('p.id_product = :id_product')
            ->andWhere('ps.id_shop = :id_shop')
            ->andWhere('ps.active = 1')
            ->andWhere('ps.visibility IN (:visibility)')
            ->setParameter('visibility', ['both', 'catalog'], \Doctrine\DBAL\Connection::PARAM_STR_ARRAY)
            ->setParameter('id_product', $idProduct)
            ->setParameter('id_shop', $idStore);

        return !empty($qb->execute()->fetchAllAssociative());
    }
}

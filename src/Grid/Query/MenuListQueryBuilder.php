<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

final class MenuListQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var Context
     */
    private Context $contextAdapter;

    /**
     * MenuListQueryBuilder constructor.
     *
     * @param Connection $connection
     * @param $dbPrefix
     * @param Context $contextAdapter
     */
    public function __construct(
        Connection $connection,
        $dbPrefix,
        Context $contextAdapter
    ) {
        parent::__construct($connection, $dbPrefix);

        $this->contextAdapter = $contextAdapter;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return QueryBuilder
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->getBaseQuery();
        $qb->select('mel.id_menu_element, mel.active, mel.name, mel.type, mel.position');

        if (\Shop::isFeatureActive() && !$this->contextAdapter->isAllShopContext()) {
            $qb->join('mel', $this->dbPrefix . 'menu_element_shop', 'mels', 'mels.id_menu_element = mel.id_menu_element')
                ->where('mels.id_shop in (' . implode(', ', $this->contextAdapter->getContextListShopID()) . ')')
                ->groupBy('mel.id_menu_element');
        }

        $this->filterByParentElement($qb, $searchCriteria->getFilters());

        $qb->orderBy(
            $searchCriteria->getOrderBy(),
            $searchCriteria->getOrderWay()
        )
            ->setFirstResult($searchCriteria->getOffset())
            ->setMaxResults($searchCriteria->getLimit());

        $qb->orderBy('position');

        return $qb;
    }

    private function filterByParentElement(QueryBuilder $qb, array $filters): void
    {
        if (isset($filters['id_parent_menu_element']) && $filters['id_parent_menu_element'] !== '') {
            $qb->andWhere('mel.id_parent_menu_element = :id_parent_menu_element')
                ->setParameter('id_parent_menu_element', $filters['id_parent_menu_element']);
        } else {
            $qb->andWhere('mel.id_parent_menu_element = :id_parent_menu_element')
                ->setParameter('id_parent_menu_element', 0);
        }
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return QueryBuilder
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->getBaseQuery();
        $qb->select('COUNT(DISTINCT mel.id_menu_element)');

        if (\Shop::isFeatureActive() && !$this->contextAdapter->isAllShopContext()) {
            $qb->join('mel', $this->dbPrefix . 'menu_element_shop', 'mels', 'mels.id_menu_element = mel.id_menu_element')
                ->where('mels.id_shop in (' . implode(', ', $this->contextAdapter->getContextListShopID()) . ')');
        }

        $this->filterByParentElement($qb, $searchCriteria->getFilters());

        return $qb;
    }

    /**
     * @return QueryBuilder
     */
    private function getBaseQuery(): QueryBuilder
    {
        return $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'menu_element', 'mel');
    }
}

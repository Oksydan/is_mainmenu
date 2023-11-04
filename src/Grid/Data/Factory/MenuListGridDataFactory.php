<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Grid\Data\Factory;

use PrestaShop\PrestaShop\Core\Grid\Data\Factory\GridDataFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

final class MenuListGridDataFactory implements GridDataFactoryInterface
{
    /**
     * @var GridDataFactoryInterface
     */
    private $menuListDataFactory;

    public function __construct(
        GridDataFactoryInterface $menuListDataFactory
    ) {
        $this->menuListDataFactory = $menuListDataFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $menuListData = $this->menuListDataFactory->getData($searchCriteria);

        return new GridData(
            new RecordCollection($menuListData->getRecords()->all()),
            $menuListData->getRecordsTotal(),
            $menuListData->getQuery()
        );
    }
}

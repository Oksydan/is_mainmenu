<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Filter;

use PrestaShop\PrestaShop\Core\Search\Filters;

/**
 * Class MenuListFilters proves default filters for our menu list grid
 */
final class MenuListFilters extends Filters
{
    /**
     * {@inheritdoc}
     */
    public static function getDefaults(): array
    {
        return [
            'limit' => 10,
            'offset' => 0,
            'orderBy' => 'position',
            'sortOrder' => 'ASC',
            'filters' => [],
        ];
    }
}

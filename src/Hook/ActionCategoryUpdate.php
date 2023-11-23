<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Hook;

use Oksydan\IsMainMenu\Handler\Category\CategoryUpdateHandler;
use PrestaShop\PrestaShop\Adapter\Validate;

class ActionCategoryUpdate extends AbstractHook
{
    private CategoryUpdateHandler $categoryUpdateHandler;

    public function __construct(
        \Is_mainmenu $module,
        \Context $context,
        CategoryUpdateHandler $categoryUpdateHandler
    ) {
        parent::__construct($module, $context);

        $this->categoryUpdateHandler = $categoryUpdateHandler;
    }

    public function execute(array $params): void
    {
        $category = $params['category'];

        if (!Validate::isLoadedObject($category)) {
            return;
        }

        $this->categoryUpdateHandler->handle($category);
    }
}

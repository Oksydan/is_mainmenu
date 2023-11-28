<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Hook;

use Oksydan\IsMainMenu\Handler\Category\CategoryDeleteHandler;
use PrestaShop\PrestaShop\Adapter\Validate;

class ActionCategoryDelete extends AbstractHook
{
    private CategoryDeleteHandler $categoryDeleteHandler;

    public function __construct(
        \Is_mainmenu $module,
        \Context $context,
        CategoryDeleteHandler $categoryDeleteHandler
    ) {
        parent::__construct($module, $context);

        $this->categoryDeleteHandler = $categoryDeleteHandler;
    }

    public function execute(array $params): void
    {
        $category = $params['category'];

        if (!Validate::isLoadedObject($category)) {
            return;
        }

        $this->categoryDeleteHandler->handle($category);
    }
}

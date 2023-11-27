<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Hook;

use Oksydan\IsMainMenu\Handler\Product\ProductDeleteHandler;
use PrestaShop\PrestaShop\Adapter\Validate;

class ActionProductDelete extends AbstractHook
{
    private ProductDeleteHandler $productDeleteHandler;

    public function __construct(
        \Is_mainmenu $module,
        \Context $context,
        ProductDeleteHandler $productDeleteHandler
    ) {
        parent::__construct($module, $context);

        $this->productDeleteHandler = $productDeleteHandler;
    }

    public function execute(array $params): void
    {
        $product = $params['product'];

        if (!Validate::isLoadedObject($product)) {
            return;
        }

        $this->productDeleteHandler->handle($product);
    }
}

<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Hook;

use Oksydan\IsMainMenu\Handler\Product\ProductUpdateHandler;
use PrestaShop\PrestaShop\Adapter\Validate;

class ActionProductSave extends AbstractHook
{
    private ProductUpdateHandler $productUpdateHandler;

    public function __construct(
        \Is_mainmenu $module,
        \Context $context,
        ProductUpdateHandler $productUpdateHandler
    ) {
        parent::__construct($module, $context);

        $this->productUpdateHandler = $productUpdateHandler;
    }

    public function execute(array $params): void
    {
        $product = $params['product'];

        if (!Validate::isLoadedObject($product)) {
            return;
        }

        $this->productUpdateHandler->handle($product);
    }
}

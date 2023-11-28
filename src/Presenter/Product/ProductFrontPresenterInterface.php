<?php

namespace Oksydan\IsMainMenu\Presenter\Product;

use PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductLazyArray;

interface ProductFrontPresenterInterface
{
    public function present(array $product): ProductLazyArray;
}

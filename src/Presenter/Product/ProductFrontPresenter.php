<?php

namespace Oksydan\IsMainMenu\Presenter\Product;

use PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductLazyArray;
use PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductPresenter;
use PrestaShop\PrestaShop\Core\Product\ProductPresentationSettings;

class ProductFrontPresenter implements ProductFrontPresenterInterface
{
    protected \Context $context;
    protected \ProductAssembler $productAssembler;
    protected ProductPresentationSettings $presentationSettings;
    protected ProductPresenter $presenter;

    public function __construct(
        \Context $context
    ) {
        $this->context = $context;
        $presenterFactory = new \ProductPresenterFactory($context);
        $this->productAssembler = new \ProductAssembler($context);
        $this->presentationSettings = $presenterFactory->getPresentationSettings();
        $this->presenter = $presenterFactory->getPresenter();
    }

    /**
     * @param array{id_product: int, id_product_attribute: int} $product
     *
     * @return ProductLazyArray
     */
    public function present(array $product): ProductLazyArray
    {
        return $this->presenter->present(
            $this->presentationSettings,
            $this->productAssembler->assembleProduct($product),
            $this->context->language
        );
    }
}

<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Controller;

use Oksydan\IsMainMenu\Form\AutocompleteRender\AutocompleteSelectedProductRender;
use Oksydan\IsMainMenu\LegacyRepository\ProductLegacyRepository;
use Oksydan\IsMainMenu\Presenter\Product\ProductAutocompletePresenter;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMenuAPIController extends FrameworkBundleAdminController
{
    public function productAutocompleteAction(Request $request): Response
    {
        $query = $request->request->get('q');
        $repository = $this->get(ProductLegacyRepository::class);
        $presenter = $this->get(ProductAutocompletePresenter::class);
        $context = \Context::getContext();

        $products = $repository->getProductsByQuery($query, $context->language->id);

        $products = array_map(function ($product) use ($presenter) {
            return $presenter->present($product);
        }, $products);

        return $this->json($products);
    }

    public function selectedProductAction(Request $request): Response
    {
        $idProduct = $request->request->get('id_product');
        $idProductAttribute = $request->request->get('id_product_attribute');
        $render = $this->get(AutocompleteSelectedProductRender::class);

        $content = $render->render([
            'id_product' => $idProduct,
            'id_product_attribute' => $idProductAttribute,
        ]);

        return $this->json([
            'content' => $content,
        ]);
    }
}

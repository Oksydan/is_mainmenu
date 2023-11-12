<?php

namespace Oksydan\IsMainMenu\Form\AutocompleteRender;

use Oksydan\IsMainMenu\Presenter\Product\ProductAutocompletePresenter;
use Oksydan\IsMainMenu\LegacyRepository\ProductLegacyRepository;
use Twig\Environment;

class AutocompleteSelectedProductRender implements RenderInterface
{
    private ProductLegacyRepository $productLegacyRepository;

    private ProductAutocompletePresenter $productAutocompletePresenter;

    private Environment $twig;

    private \Context $context;

    public function __construct(
        Environment $twig,
        ProductLegacyRepository $productLegacyRepository,
        ProductAutocompletePresenter $productAutocompletePresenter,
        \Context $context
    ) {
        $this->twig = $twig;
        $this->productLegacyRepository = $productLegacyRepository;
        $this->productAutocompletePresenter = $productAutocompletePresenter;
        $this->context = $context;
    }


    public function render(array $product): string
    {
        $productData = $this->productLegacyRepository->getProductDataByIdAndIdAttribute(
            (int) $product['id_product'],
            (int) $product['id_product_attribute'],
            $this->context->language->id
        );
        $productPresented = $this->productAutocompletePresenter->present($productData);

        return $this->twig->render('@Modules/is_mainmenu/views/templates/admin/widget/product_autocomplete_result.html.twig', [
            'product' => $productPresented,
        ]);
    }
}

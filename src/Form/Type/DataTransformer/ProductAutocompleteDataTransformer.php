<?php

namespace Oksydan\IsMainMenu\Form\Type\DataTransformer;

use Oksydan\IsMainMenu\Form\AutocompleteRender\AutocompleteSelectedProductRender;
use Symfony\Component\Form\DataTransformerInterface;



class ProductAutocompleteDataTransformer implements DataTransformerInterface
{
    /*
     * @var AutocompleteSelectedProductRender
     */
    private AutocompleteSelectedProductRender $autocompleteSelectedProductRender;

    public function __construct(
        AutocompleteSelectedProductRender $autocompleteSelectedProductRender
    ) {
        $this->autocompleteSelectedProductRender = $autocompleteSelectedProductRender;
    }

    public function transform($value): array
    {
        $idProduct = !empty($value['id_product']) ? (int) $value['id_product'] : 0;
        $idProductAttribute = !empty($value['id_product_attribute']) ? (int) $value['id_product_attribute'] : 0;
        $productSelected = '';

        if ($idProduct) {
            $productSelected = $this->autocompleteSelectedProductRender->render([
                'id_product' => $idProduct,
                'id_product_attribute' => $idProductAttribute,
            ]);
        }

        return [
            'product_selected' => $productSelected,
            'id_product' => !empty($value['id_product']) ? (int) $value['id_product'] : 0,
            'id_product_attribute' => !empty($value['id_product_attribute']) ? (int) $value['id_product_attribute'] : 0,
        ];
    }

    public function reverseTransform($value): array
    {
        return [
            'id_product' => (int) $value['id_product'],
            'id_product_attribute' => (int) $value['id_product_attribute'],
        ];
    }
}

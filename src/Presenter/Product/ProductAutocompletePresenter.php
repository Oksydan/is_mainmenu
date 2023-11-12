<?php

namespace Oksydan\IsMainMenu\Presenter\Product;

use Oksydan\IsMainMenu\LegacyRepository\ProductLegacyRepository;

class ProductAutocompletePresenter implements ProductAutocompletePresenterInterface
{
    private ProductLegacyRepository $productRepository;

    private \Context $context;

    public function __construct(
        ProductLegacyRepository $productRepository,
        \Context $context
    )
    {
        $this->productRepository = $productRepository;
        $this->context = $context;
    }


    public function present(array $product): array
    {
        $product['id_product_attribute'] = (int) $product['id_product_attribute'];
        $product['name'] = $this->formatProductName($product);
        $product['image'] = $this->getProductImage($product);

        return $product;
    }

    private function getProductImage(array $product): string
    {
        $idProductAttribute = $product['id_product_attribute'];
        $imageTypes = \ImageType::getImagesTypes('products', true);

        if ($imageTypes) {
            $imageType = end($imageTypes);
        } else {
            return '';
        }

        if ($idProductAttribute) {
            $combinationImages = $this->productRepository->getProductCombinationImagesForIdProductAttribute(
                (int) $product['id_product'],
                (int) $idProductAttribute
            );

            if (!empty($combinationImages)) {
                return $this->context->link->getImageLink(
                    $product['link_rewrite'],
                    $combinationImages[0]['id_image'],
                    $imageType['name']
                );
            }
        }

        $coverId = $this->productRepository->getProductCoverForIdProduct((int) $product['id_product']);

        if ($coverId) {
            return $this->context->link->getImageLink(
                $product['link_rewrite'],
                $coverId,
                $imageType['name']
            );
        }

        return '';
    }

    private function formatProductName(array $product): string
    {
        $idProductAttribute = $product['id_product_attribute'];

        if ($idProductAttribute) {
            if (!empty($product['attribute_reference'])) {
                $product['name'] .= ' #' . $product['attribute_reference'];
            }

            $combinations = $this->productRepository->getProductCombinationForIdProductAttribute(
                (int) $product['id_product'],
                (int) $idProductAttribute,
                (int) $this->context->language->id
            );

            $combinationsNames = array_reduce($combinations, function ($combinationReduced, $combination) {
                $combinationReduced[] = $combination['group_name'] . ': ' . $combination['attribute_name'];
                return $combinationReduced;
            }, []);

            return $product['name'] . ' - ' . implode(', ', $combinationsNames);
        } else {
            if (!empty($product['reference'])) {
                $product['name'] .= ' #' . $product['reference'];
            }

            return $product['name'];
        }
    }
}

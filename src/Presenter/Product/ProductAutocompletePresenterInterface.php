<?php

namespace Oksydan\IsMainMenu\Presenter\Product;

interface ProductAutocompletePresenterInterface
{
    public function present(array $product): array;
}

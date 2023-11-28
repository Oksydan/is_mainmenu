<?php

namespace Oksydan\IsMainMenu\Handler\Product;

interface ProductHandlerInterface
{
    public function handle(\Product $product): void;
}

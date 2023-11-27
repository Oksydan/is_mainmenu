<?php

namespace Oksydan\IsMainMenu\Handler\Category;

interface CategoryHandlerInterface
{
    public function handle(\Category $category): void;
}

<?php

namespace Oksydan\IsMainMenu\Form\AutocompleteRender;

interface RenderInterface
{
    public function render(array $product): string;
}

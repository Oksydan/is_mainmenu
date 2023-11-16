<?php

namespace Oksydan\IsMainMenu\View\Front;

interface MenuFrontRenderInterface
{
    /**
     * @param int $idMenuElement
     * @return string
     */
    public function render(int $idMenuElement): string;
}

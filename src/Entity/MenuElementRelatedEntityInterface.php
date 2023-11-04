<?php

namespace Oksydan\IsMainMenu\Entity;

interface MenuElementRelatedEntityInterface
{
    /**
     * @return MenuElement
     */
    public function getMenuElement(): MenuElement;
}

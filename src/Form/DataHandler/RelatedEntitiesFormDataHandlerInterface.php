<?php

namespace Oksydan\IsMainMenu\Form\DataHandler;

use Oksydan\IsMainMenu\Entity\MenuElement;
use Oksydan\IsMainMenu\Entity\MenuElementRelatedEntityInterface;

interface RelatedEntitiesFormDataHandlerInterface
{
    /**
     * @param MenuElement $menuElement
     * @param array $data
     *
     * @return MenuElementRelatedEntityInterface
     */
    public function handle(MenuElement $menuElement, array $data): MenuElementRelatedEntityInterface;
}

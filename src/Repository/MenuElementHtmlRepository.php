<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Repository;

use Doctrine\ORM\EntityRepository;
use Oksydan\IsMainMenu\Entity\MenuElement;
use Oksydan\IsMainMenu\Entity\MenuElementHtml;

class MenuElementHtmlRepository extends EntityRepository
{
    public function findMenuElementHtmlByMenuElement(MenuElement $menuElement): ?MenuElementHtml
    {
        return $this->findOneBy(['menuElement' => $menuElement]);
    }
}

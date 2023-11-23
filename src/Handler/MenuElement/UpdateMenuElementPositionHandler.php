<?php

namespace Oksydan\IsMainMenu\Handler\MenuElement;

use Oksydan\IsMainMenu\Cache\ModuleCache;
use Oksydan\IsMainMenu\Entity\MenuElement;
use Oksydan\IsMainMenu\Repository\MenuElementRepository;
use PrestaShop\PrestaShop\Core\Grid\Position\GridPositionUpdater;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionDefinition;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionUpdateFactory;

class UpdateMenuElementPositionHandler
{
    private GridPositionUpdater $gridPositionUpdater;

    private MenuElementRepository $menuElementRepository;

    private ModuleCache $moduleCache;

    public function __construct(
        GridPositionUpdater $gridPositionUpdater,
        MenuElementRepository $menuElementRepository,
        ModuleCache $moduleCache
    ) {
        $this->gridPositionUpdater = $gridPositionUpdater;
        $this->menuElementRepository = $menuElementRepository;
        $this->moduleCache = $moduleCache;
    }

    public function handle(array $positions): int
    {
        $firstElement = reset($positions);
        $elementId = $firstElement['rowId'];
        $element = $this->menuElementRepository->find((int) $elementId);
        $parentId = $this->menuElementRepository->getRootElement()->getId();

        if ($element instanceof MenuElement) {
            $parentElement = $element->getParentMenuElement();

            if ($parentElement instanceof MenuElement) {
                $parentId = $parentElement->getId();
            }
        }

        $positionsData = [
            'positions' => $positions,
            'id_parent_menu_element' => $parentId,
        ];

        $positionDefinition = new PositionDefinition(
            'menu_element',
            'id_menu_element',
            'position',
            'id_parent_menu_element',
        );

        $positionUpdateFactory = new PositionUpdateFactory(
            'positions',
            'rowId',
            'oldPosition',
            'newPosition',
            'id_parent_menu_element'
        );

        $positionUpdate = $positionUpdateFactory->buildPositionUpdate($positionsData, $positionDefinition);

        $this->gridPositionUpdater->update($positionUpdate);

        $this->moduleCache->clearCache();

        return $parentId;
    }
}

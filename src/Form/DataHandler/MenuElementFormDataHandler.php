<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Form\DataHandler;

use Doctrine\ORM\EntityManagerInterface;
use Oksydan\IsMainMenu\Cache\ModuleCache;
use Oksydan\IsMainMenu\Entity\MenuElement;
use Oksydan\IsMainMenu\Menu\MenuLayoutGrid;
use Oksydan\IsMainMenu\Repository\MenuElementRepository;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\FormDataHandlerInterface;
use PrestaShopBundle\Entity\Shop;

class MenuElementFormDataHandler implements FormDataHandlerInterface
{
    /*
     * @var MenuElementBannerDataHandler
     */
    private MenuElementBannerDataHandler $menuElementBannerDataHandler;

    /*
     * @var MenuElementCategoryDataHandler
     */
    private MenuElementCategoryDataHandler $menuElementCategoryDataHandler;

    /*
     * @var MenuElementCustomDataHandler
     */
    private MenuElementCustomDataHandler $menuElementCustomDataHandler;

    /*
     * @var MenuElementHtmlDataHandler
     */
    private MenuElementHtmlDataHandler $menuElementHtmlDataHandler;

    /*
     * @var MenuElementCmsDataHandler
     */
    private MenuElementCmsDataHandler $menuElementCmsDataHandler;

    private MenuElementProductDataHandler $menuElementProductDataHandler;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /*
     * @var EntityRepository
     */
    private MenuElementRepository $menuElementRepository;

    /*
     * @var ModuleCache
     */
    private ModuleCache $moduleCache;

    public function __construct(
        MenuElementBannerDataHandler $menuElementBannerDataHandler,
        MenuElementCategoryDataHandler $menuElementCategoryDataHandler,
        MenuElementCustomDataHandler $menuElementCustomDataHandler,
        MenuElementHtmlDataHandler $menuElementHtmlDataHandler,
        MenuElementCmsDataHandler $menuElementCmsDataHandler,
        MenuElementProductDataHandler $menuElementProductDataHandler,
        EntityManagerInterface $entityManager,
        MenuElementRepository $menuElementRepository,
        ModuleCache $moduleCache
    ) {
        $this->menuElementBannerDataHandler = $menuElementBannerDataHandler;
        $this->menuElementCategoryDataHandler = $menuElementCategoryDataHandler;
        $this->menuElementCustomDataHandler = $menuElementCustomDataHandler;
        $this->menuElementHtmlDataHandler = $menuElementHtmlDataHandler;
        $this->menuElementCmsDataHandler = $menuElementCmsDataHandler;
        $this->menuElementProductDataHandler = $menuElementProductDataHandler;
        $this->entityManager = $entityManager;
        $this->menuElementRepository = $menuElementRepository;
        $this->moduleCache = $moduleCache;
    }

    public function create(array $data)
    {
        $menuElement = new MenuElement();

        $menuElement->setName($data['name']);
        $menuElement->setActive($data['active']);
        $menuElement->setCssClass($data['css_class'] ?? '');
        $menuElement->setType($data['type']);
        $menuElement->setDisplayMobile($data['display_mobile'] ?? true);
        $menuElement->setDisplayDesktop($data['display_desktop'] ?? true);
        $menuElement->setPosition(0);
        $menuElement->setGridType($data['grid_type'] ?? MenuLayoutGrid::GRID_12);
        $this->addAssociatedShops($menuElement, $data['shop_association'] ?? null);

        if ($data['id_parent_element']) {
            $parentMenuElement = $this->menuElementRepository->find($data['id_parent_element']);

            if ($parentMenuElement instanceof MenuElement) {
                $menuElement->setParentMenuElement($parentMenuElement);
                $menuElement->setDepth($parentMenuElement->getDepth() + 1);
                $menuElement->setPosition($this->menuElementRepository->getHighestPosition($parentMenuElement) + 1);
            }
        }

        $this->entityManager->persist($menuElement);
        $this->entityManager->flush();

        $this->clearCache();

        return $menuElement->getId();
    }

    public function update($id, array $data)
    {
        $menuElement = $this->menuElementRepository->find($id);

        $menuElement->setName($data['name']);
        $menuElement->setActive($data['active']);
        $menuElement->setCssClass($data['css_class'] ?? '');
        $menuElement->setDisplayMobile($data['display_mobile'] ?? true);
        $menuElement->setDisplayDesktop($data['display_desktop'] ?? true);
        $menuElement->setGridType($data['grid_type'] ?? MenuLayoutGrid::GRID_12);
        $this->addAssociatedShops($menuElement, $data['shop_association'] ?? null);

        $menuRelatedElement = null;

        switch ($menuElement->getType()) {
            case MenuELement::TYPE_LINK:
                $menuRelatedElement = $this->menuElementCustomDataHandler->handle($menuElement, $data);
                break;
            case MenuELement::TYPE_CATEGORY:
                $menuRelatedElement = $this->menuElementCategoryDataHandler->handle($menuElement, $data);
                break;
            case MenuELement::TYPE_BANNER:
                $menuRelatedElement = $this->menuElementBannerDataHandler->handle($menuElement, $data);
                break;
            case MenuELement::TYPE_HTML:
                $menuRelatedElement = $this->menuElementHtmlDataHandler->handle($menuElement, $data);
                break;
            case MenuELement::TYPE_CMS:
                $menuRelatedElement = $this->menuElementCmsDataHandler->handle($menuElement, $data);
                break;
            case MenuELement::TYPE_PRODUCT:
                $menuRelatedElement = $this->menuElementProductDataHandler->handle($menuElement, $data);
                break;
        }

        if ($menuRelatedElement) {
            $this->entityManager->persist($menuRelatedElement);
        }

        $this->entityManager->persist($menuElement);
        $this->entityManager->flush();

        $this->clearCache();

        return $menuElement->getId();
    }

    /**
     * @param MenuElement $menuElement
     * @param array|null $shopIdList
     */
    private function addAssociatedShops(MenuElement &$menuElement, array $shopIdList = null): void
    {
        $menuElement->clearShops();

        if (empty($shopIdList)) {
            return;
        }

        foreach ($shopIdList as $shopId) {
            $shop = $this->entityManager->getRepository(Shop::class)->find($shopId);
            $menuElement->addShop($shop);
        }
    }

    private function clearCache(): void
    {
        $this->moduleCache->clearCache();
    }
}

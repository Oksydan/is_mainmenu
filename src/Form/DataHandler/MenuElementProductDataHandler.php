<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Form\DataHandler;

use Oksydan\IsMainMenu\Entity\MenuElement;
use Oksydan\IsMainMenu\Entity\MenuElementProduct;
use Oksydan\IsMainMenu\Entity\MenuElementRelatedEntityInterface;
use Oksydan\IsMainMenu\Repository\MenuElementProductRepository;

class MenuElementProductDataHandler implements RelatedEntitiesFormDataHandlerInterface
{
    /*
     * @var MenuElementCustomRepository
     */
    private MenuElementProductRepository $menuElementProductRepository;

    /*
     * @var array
     */
    private array $languages;

    public function __construct(
        MenuElementProductRepository $menuElementProductRepository,
    ) {
        $this->menuElementProductRepository = $menuElementProductRepository;
    }

    public function handle(MenuElement $menuElement, array $data): MenuElementRelatedEntityInterface
    {
        /* @var MenuElementProduct|null $menuElementProduct */
        $menuElementProduct = $this->menuElementProductRepository->findOneBy([
            'menuElement' => $menuElement->getId(),
        ]);

        $product = $data['product'];
        $idProduct = $product['id_product'];
        $idProductAttribute = $product['id_product_attribute'];

        if (!($menuElementProduct instanceof MenuElementProduct)) {
            $menuElementProduct = new MenuElementProduct();
            $menuElementProduct->setMenuElement($menuElement);
        }

        $menuElementProduct->setIdProduct($idProduct);
        $menuElementProduct->setIdProductAttribute($idProductAttribute);

        return $menuElementProduct;
    }
}

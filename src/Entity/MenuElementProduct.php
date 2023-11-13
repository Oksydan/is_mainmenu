<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Oksydan\IsMainMenu\Repository\MenuElementProductRepository")
 *
 * @ORM\Table()
 */
class MenuElementProduct implements MenuElementRelatedEntityInterface
{
    /**
     * @var int
     *
     * @ORM\Id
     *
     * @ORM\Column(name="id_menu_element_product", type="integer")
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var MenuElement
     *
     * @ORM\ManyToOne(targetEntity="Oksydan\IsMainMenu\Entity\MenuElement")
     *
     * @ORM\JoinColumn(name="id_menu_element", referencedColumnName="id_menu_element", nullable=false, onDelete="CASCADE")
     */
    private $menuElement;

    /**
     * @var int
     *
     * @ORM\Column(name="id_product", type="integer")
     */
    private $id_product;

    /**
     * @var int
     *
     * @ORM\Column(name="id_product_attribute", type="integer")
     */
    private $id_product_attribute;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return MenuElement
     */
    public function getMenuElement(): MenuElement
    {
        return $this->menuElement;
    }

    /**
     * @param MenuElement $menuElement
     *
     * @return MenuElementProduct $this
     */
    public function setMenuElement(MenuElement $menuElement): MenuElementProduct
    {
        $this->menuElement = $menuElement;

        return $this;
    }

    /**
     * @return int
     */
    public function getIdProduct(): int
    {
        return $this->id_product;
    }

    /**
     * @param int $id_product
     *
     * @return MenuElementProduct $this
     */
    public function setIdProduct(int $id_product): MenuElementProduct
    {
        $this->id_product = $id_product;

        return $this;
    }

    /**
     * @return int
     */
    public function getIdProductAttribute(): int
    {
        return $this->id_product_attribute;
    }

    /**
     * @param int $id_product_attribute
     *
     * @return MenuElementProduct $this
     */
    public function setIdProductAttribute(int $id_product_attribute): MenuElementProduct
    {
        $this->id_product_attribute = $id_product_attribute;

        return $this;
    }
}

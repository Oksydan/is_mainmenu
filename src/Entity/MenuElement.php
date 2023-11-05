<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use PrestaShopBundle\Entity\Shop;

/**
 * @ORM\Entity(repositoryClass="Oksydan\IsMainMenu\Repository\MenuElementRepository")
 *
 * @ORM\Table()
 */
class MenuElement
{
    const TYPE_CATEGORY = 'category';
    const TYPE_LINK = 'custom';
    const TYPE_BANNER = 'banner';

    const TYPE_HTML = 'html';

    const TYPE_CMS = 'cms';

    // USED ONLY FOR ROOT ELEMENT
    const TYPE_ROOT = 'root';

    const TYPE_CHOICES = [
        self::TYPE_CATEGORY,
        self::TYPE_LINK,
        self::TYPE_BANNER,
        self::TYPE_HTML,
        self::TYPE_CMS,
    ];

    /**
     * @var int
     *
     * @ORM\Id
     *
     * @ORM\Column(name="id_menu_element", type="integer")
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var MenuElement
     *
     * @ORM\ManyToOne(targetEntity="Oksydan\IsMainMenu\Entity\MenuElement")
     *
     * @ORM\JoinColumn(name="id_parent_menu_element", referencedColumnName="id_menu_element", nullable=true)
     */
    private $parentMenuElement;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    /**
     * @var bool
     *
     * @ORM\Column(name="display_mobile", type="boolean")
     */
    private $displayMobile;

    /**
     * @var bool
     *
     * @ORM\Column(name="display_desktop", type="boolean")
     */
    private $displayDesktop;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer")
     */
    private $position;

    /**
     * @var int
     *
     * @ORM\Column(name="depth", type="integer")
     */
    private $depth;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_root", type="boolean")
     */
    private $isRoot;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="text")
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="text")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="css_class", type="text")
     */
    private $cssClass;

    /**
     * @ORM\ManyToMany(targetEntity="PrestaShopBundle\Entity\Shop", cascade={"persist"})
     *
     * @ORM\JoinTable(
     *      joinColumns={@ORM\JoinColumn(name="id_menu_element", referencedColumnName="id_menu_element")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_shop", referencedColumnName="id_shop", onDelete="CASCADE")}
     * )
     */
    private $shops;

    public function __construct()
    {
        $this->shops = new ArrayCollection();
        $this->parentMenuElement = null;
        $this->isRoot = false;
        $this->displayMobile = true;
        $this->displayDesktop = true;
    }

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
    public function getParentMenuElement(): ?MenuElement
    {
        return $this->parentMenuElement;
    }

    /**
     * @param MenuElement $parentMenuElement
     *
     * @return MenuElement $this
     */
    public function setParentMenuElement(MenuElement $parentMenuElement): MenuElement
    {
        $this->parentMenuElement = $parentMenuElement;

        return $this;
    }

    /**
     * @return bool
     */
    public function getActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     *
     * @return MenuElement $this
     */
    public function setActive(bool $active): MenuElement
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return bool
     */
    public function getDisplayMobile(): bool
    {
        return $this->displayMobile;
    }

    /**
     * @param bool $displayMobile
     *
     * @return MenuElement $this
     */
    public function setDisplayMobile(bool $displayMobile): MenuElement
    {
        $this->displayMobile = $displayMobile;

        return $this;
    }

    /**
     * @return bool
     */
    public function getDisplayDesktop(): bool
    {
        return $this->displayDesktop;
    }

    /**
     * @param bool $displayDesktop
     *
     * @return MenuElement $this
     */
    public function setDisplayDesktop(bool $displayDesktop): MenuElement
    {
        $this->displayDesktop = $displayDesktop;

        return $this;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @param int $position
     *
     * @return MenuElement $this
     */
    public function setPosition(int $position): MenuElement
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return int
     */
    public function getDepth(): int
    {
        return $this->depth;
    }

    /**
     * @param int $depth
     *
     * @return MenuElement $this
     */
    public function setDepth(int $depth): MenuElement
    {
        $this->depth = $depth;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsRoot(): bool
    {
        return $this->isRoot;
    }

    /**
     * @param bool $isRoot
     *
     * @return MenuElement $this
     */
    public function setIsRoot(bool $isRoot): MenuElement
    {
        $this->isRoot = $isRoot;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return MenuElement $this
     */
    public function setType(string $type): MenuElement
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return MenuElement $this
     */
    public function setName(string $name): MenuElement
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getCssClass(): string
    {
        return $this->cssClass;
    }

    /**
     * @param string $cssClass
     *
     * @return MenuElement $this
     */
    public function setCssClass(string $cssClass): MenuElement
    {
        $this->cssClass = $cssClass;

        return $this;
    }

    /**
     * @param Shop $shop
     *
     * @return MenuElement $this
     */
    public function addShop(Shop $shop): MenuElement
    {
        $this->shops[] = $shop;

        return $this;
    }

    /**
     * @param Shop $shop
     *
     * @return MenuElement $this
     */
    public function removeShop(Shop $shop): MenuElement
    {
        $this->shops->removeElement($shop);

        return $this;
    }

    /**
     * @return Collection
     */
    public function getShops(): Collection
    {
        return $this->shops;
    }

    /**
     * @return MenuElement $this
     */
    public function clearShops(): MenuElement
    {
        $this->shops->clear();

        return $this;
    }
}

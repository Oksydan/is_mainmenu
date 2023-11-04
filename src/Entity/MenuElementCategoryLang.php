<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Entity;

use Doctrine\ORM\Mapping as ORM;
use PrestaShopBundle\Entity\Lang;

/**
 * @ORM\Table()
 *
 * @ORM\Entity
 */
class MenuElementCategoryLang
{
    /**
     * @var MenuElementCategory
     *
     * @ORM\Id
     *
     * @ORM\ManyToOne(targetEntity="Oksydan\IsMainMenu\Entity\MenuElementCategory", inversedBy="menuElementCategoryLangs")
     *
     * @ORM\JoinColumn(name="id_menu_element_category", referencedColumnName="id_menu_element_category", nullable=false)
     */
    private $menuElementCategory;

    /**
     * @var Lang
     *
     * @ORM\Id
     *
     * @ORM\ManyToOne(targetEntity="PrestaShopBundle\Entity\Lang")
     *
     * @ORM\JoinColumn(name="id_lang", referencedColumnName="id_lang", nullable=false, onDelete="CASCADE")
     */
    private $lang;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="text")
     */
    private $name;

    /**
     * @return MenuElementCategory
     */
    public function getMenuElementCategory(): MenuElementCategory
    {
        return $this->menuElementCategory;
    }

    /**
     * @param MenuElementCategory $menuElementCategory
     *
     * @return MenuElementCategoryLang $this
     */
    public function setMenuElementCategory(MenuElementCategory $menuElementCategory): MenuElementCategoryLang
    {
        $this->menuElementCategory = $menuElementCategory;

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
     * @return MenuElementCategoryLang $this
     */
    public function setName(string $name): MenuElementCategoryLang
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Lang
     */
    public function getLang(): Lang
    {
        return $this->lang;
    }

    /**
     * @param Lang $lang
     *
     * @return MenuElementCategoryLang $this
     */
    public function setLang(Lang $lang): MenuElementCategoryLang
    {
        $this->lang = $lang;

        return $this;
    }
}

<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Oksydan\IsMainMenu\Repository\MenuElementCategoryRepository")
 *
 * @ORM\Table()
 */
class MenuElementCategory implements MenuElementRelatedEntityInterface
{
    /**
     * @var int
     *
     * @ORM\Id
     *
     * @ORM\Column(name="id_menu_element_category", type="integer")
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
     * @ORM\Column(name="id_category", type="integer")
     */
    private $idCategory;

    /**
     * @ORM\OneToMany(targetEntity="Oksydan\IsMainMenu\Entity\MenuElementCategoryLang", cascade={"persist", "remove"}, mappedBy="menuElementCategory")
     */
    private $menuElementCategoryLangs;

    public function __construct()
    {
        $this->menuElementCategoryLangs = new ArrayCollection();
    }

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
     * @return MenuElementCategory $this
     */
    public function setMenuElement(MenuElement $menuElement): MenuElementCategory
    {
        $this->menuElement = $menuElement;

        return $this;
    }

    /**
     * @return int
     */
    public function getIdCategory(): int
    {
        return $this->idCategory;
    }

    /**
     * @param int $idCategory
     *
     * @return MenuElementCategory $this
     */
    public function setIdCategory(int $idCategory): MenuElementCategory
    {
        $this->idCategory = $idCategory;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getMenuElementCategoryLangs()
    {
        return $this->menuElementCategoryLangs;
    }

    /**
     * @param int $langId
     *
     * @return MenuElementCategoryLang|null
     */
    public function getMenuElementCategoryLangsByLangId(int $langId)
    {
        foreach ($this->menuElementCategoryLangs as $menuElementCategoryLang) {
            if ($langId === $menuElementCategoryLang->getLang()->getId()) {
                return $menuElementCategoryLang;
            }
        }

        return null;
    }

    /**
     * @param MenuElementCategoryLang $menuElementCategoryLang
     *
     * @return MenuElementCategory $this
     */
    public function addMenuElementCategoryLang(MenuElementCategoryLang $menuElementCategoryLang): MenuElementCategory
    {
        $menuElementCategoryLang->setMenuElementCategory($this);
        $this->menuElementCategoryLangs->add($menuElementCategoryLang);

        return $this;
    }
}

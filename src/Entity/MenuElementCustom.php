<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Oksydan\IsMainMenu\Repository\MenuElementCustomRepository")
 *
 * @ORM\Table()
 */
class MenuElementCustom implements MenuElementRelatedEntityInterface
{
    /**
     * @var int
     *
     * @ORM\Id
     *
     * @ORM\Column(name="id_menu_element_custom", type="integer")
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
     * @ORM\OneToMany(targetEntity="Oksydan\IsMainMenu\Entity\MenuElementCustomLang", cascade={"persist", "remove"}, mappedBy="menuElementCustom")
     */
    private $menuElementCustomLangs;

    public function __construct()
    {
        $this->menuElementCustomLangs = new ArrayCollection();
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
    public function getMenuElement(): MenuElement
    {
        return $this->menuElement;
    }

    /**
     * @param MenuElement $menuElement
     *
     * @return MenuElementCustom $this
     */
    public function setMenuElement(MenuElement $menuElement): MenuElementCustom
    {
        $this->menuElement = $menuElement;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getMenuElementCustomLangs()
    {
        return $this->menuElementCustomLangs;
    }

    /**
     * @param int $langId
     *
     * @return MenuElementCustomLang|null
     */
    public function getMenuElementCustomLangsByLangId(int $langId)
    {
        foreach ($this->menuElementCustomLangs as $menuElementCustomLang) {
            if ($langId === $menuElementCustomLang->getLang()->getId()) {
                return $menuElementCustomLang;
            }
        }

        return null;
    }

    /**
     * @param MenuElementCustomLang $menuElementCustomLang
     *
     * @return MenuElementCustom $this
     */
    public function addMenuElementCustomLang(MenuElementCustomLang $menuElementCustomLang): MenuElementCustom
    {
        $menuElementCustomLang->setMenuElementCustom($this);
        $this->menuElementCustomLangs->add($menuElementCustomLang);

        return $this;
    }
}

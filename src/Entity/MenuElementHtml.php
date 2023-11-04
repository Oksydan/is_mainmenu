<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Oksydan\IsMainMenu\Repository\MenuElementHtmlRepository")
 *
 * @ORM\Table()
 */
class MenuElementHtml implements MenuElementRelatedEntityInterface
{
    /**
     * @var int
     *
     * @ORM\Id
     *
     * @ORM\Column(name="id_menu_element_html", type="integer")
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
     * @ORM\OneToMany(targetEntity="Oksydan\IsMainMenu\Entity\MenuElementHtmlLang", cascade={"persist", "remove"}, mappedBy="menuElementHtml")
     */
    private $menuElementHtmlLangs;

    public function __construct()
    {
        $this->menuElementHtmlLangs = new ArrayCollection();
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
     * @return MenuElementHtml $this
     */
    public function setMenuElement(MenuElement $menuElement): MenuElementHtml
    {
        $this->menuElement = $menuElement;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getMenuElementHtmlLangs()
    {
        return $this->menuElementHtmlLangs;
    }

    /**
     * @param int $langId
     *
     * @return MenuElementHtmlLang|null
     */
    public function getMenuElementHtmlLangsByLangId(int $langId)
    {
        foreach ($this->menuElementHtmlLangs as $menuElementHtmlLang) {
            if ($langId === $menuElementHtmlLang->getLang()->getId()) {
                return $menuElementHtmlLang;
            }
        }

        return null;
    }

    /**
     * @param MenuElementHtmlLang $menuElementHtmlLang
     *
     * @return MenuElementHtml $this
     */
    public function addMenuElementHtmlLang(MenuElementHtmlLang $menuElementHtmlLang): MenuElementHtml
    {
        $menuElementHtmlLang->setMenuElementHtml($this);
        $this->menuElementHtmlLangs->add($menuElementHtmlLang);

        return $this;
    }
}

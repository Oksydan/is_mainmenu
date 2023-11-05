<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Oksydan\IsMainMenu\Repository\MenuElementCmsRepository")
 *
 * @ORM\Table()
 */
class MenuElementCms implements MenuElementRelatedEntityInterface
{
    /**
     * @var int
     *
     * @ORM\Id
     *
     * @ORM\Column(name="id_menu_element_cms", type="integer")
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
     * @ORM\OneToMany(targetEntity="Oksydan\IsMainMenu\Entity\MenuElementCmsLang", cascade={"persist", "remove"}, mappedBy="menuElementCms")
     */
    private $menuElementCmsLangs;

    /**
     * @var int
     *
     * @ORM\Column(name="id_cms", type="integer")
     */
    private $idCMS;

    public function __construct()
    {
        $this->menuElementCmsLangs = new ArrayCollection();
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
     * @return MenuElementCms $this
     */
    public function setMenuElement(MenuElement $menuElement): MenuElementCms
    {
        $this->menuElement = $menuElement;

        return $this;
    }

    /**
     * @return int
     */
    public function getIdCMS(): int
    {
        return $this->idCMS;
    }

    /**
     * @param int $idCMS
     *
     * @return MenuElementCms $this
     */
    public function setIdCMS(int $idCMS): MenuElementCms
    {
        $this->idCMS = $idCMS;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getMenuElementCmsLangs()
    {
        return $this->menuElementCmsLangs;
    }

    /**
     * @param int $langId
     *
     * @return MenuElementCmsLang|null
     */
    public function getMenuElementCmsLangsByLangId(int $langId)
    {
        foreach ($this->menuElementCmsLangs as $menuElementCmsLang) {
            if ($langId === $menuElementCmsLang->getLang()->getId()) {
                return $menuElementCmsLang;
            }
        }

        return null;
    }

    /**
     * @param MenuElementCmsLang $menuElementCmsLang
     *
     * @return MenuElementCms $this
     */
    public function addMenuElementCmsLang(MenuElementCmsLang $menuElementCmsLang): MenuElementCms
    {
        $menuElementCmsLang->setMenuElementCms($this);
        $this->menuElementCmsLangs->add($menuElementCmsLang);

        return $this;
    }
}

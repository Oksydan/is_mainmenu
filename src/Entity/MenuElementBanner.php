<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Oksydan\IsMainMenu\Repository\MenuElementBannerRepository")
 *
 * @ORM\Table()
 */
class MenuElementBanner implements MenuElementRelatedEntityInterface
{
    /**
     * @var int
     *
     * @ORM\Id
     *
     * @ORM\Column(name="id_menu_element_banner", type="integer")
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
     * @ORM\OneToMany(targetEntity="Oksydan\IsMainMenu\Entity\MenuElementBannerLang", cascade={"persist", "remove"}, mappedBy="menuElementBanner")
     */
    private $menuElementBannerLangs;

    public function __construct()
    {
        $this->menuElementBannerLangs = new ArrayCollection();
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
     * @return MenuElementBanner $this
     */
    public function setMenuElement(MenuElement $menuElement): MenuElementBanner
    {
        $this->menuElement = $menuElement;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getMenuElementBannerLangs()
    {
        return $this->menuElementBannerLangs;
    }

    /**
     * @param int $langId
     *
     * @return MenuElementBannerLang|null
     */
    public function getMenuElementBannerLangsByLangId(int $langId)
    {
        foreach ($this->menuElementBannerLangs as $menuElementBannerLang) {
            if ($langId === $menuElementBannerLang->getLang()->getId()) {
                return $menuElementBannerLang;
            }
        }

        return null;
    }

    /**
     * @param MenuElementBannerLang $menuElementBannerLang
     *
     * @return MenuElementBanner $this
     */
    public function addMenuElementBannerLang(MenuElementBannerLang $menuElementBannerLang): MenuElementBanner
    {
        $menuElementBannerLang->setMenuElementBanner($this);
        $this->menuElementBannerLangs->add($menuElementBannerLang);

        return $this;
    }
}

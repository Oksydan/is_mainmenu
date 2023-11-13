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
class MenuElementCmsLang
{
    /**
     * @var MenuElementCms
     *
     * @ORM\Id
     *
     * @ORM\ManyToOne(targetEntity="Oksydan\IsMainMenu\Entity\MenuElementCms", inversedBy="menuElementCmsLangs")
     *
     * @ORM\JoinColumn(name="id_menu_element_cms", referencedColumnName="id_menu_element_cms", nullable=false)
     */
    private $menuElementCms;

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
     * @return MenuElementCms
     */
    public function getMenuElementCms(): MenuElementCms
    {
        return $this->menuElementCms;
    }

    /**
     * @param MenuElementCms $menuElementCms
     *
     * @return MenuElementCmsLang $this
     */
    public function setMenuElementCms(MenuElementCms $menuElementCms): MenuElementCmsLang
    {
        $this->menuElementCms = $menuElementCms;

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
     * @return MenuElementCmsLang $this
     */
    public function setName(string $name): MenuElementCmsLang
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
     * @return MenuElementCmsLang $this
     */
    public function setLang(Lang $lang): MenuElementCmsLang
    {
        $this->lang = $lang;

        return $this;
    }
}

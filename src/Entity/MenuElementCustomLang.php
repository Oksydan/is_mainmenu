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
class MenuElementCustomLang
{
    /**
     * @var MenuElementCustom
     *
     * @ORM\Id
     *
     * @ORM\ManyToOne(targetEntity="Oksydan\IsMainMenu\Entity\MenuElementCustom", inversedBy="menuElementCustomLangs")
     *
     * @ORM\JoinColumn(name="id_menu_element_custom", referencedColumnName="id_menu_element_custom", nullable=false)
     */
    private $menuElementCustom;

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
     * @var string
     *
     * @ORM\Column(name="url", type="text")
     */
    private $url;

    /**
     * @return MenuElementCustom
     */
    public function getMenuElementCustom(): MenuElementCustom
    {
        return $this->menuElementCustom;
    }

    /**
     * @param MenuElementCustom $menuElementCustom
     *
     * @return MenuElementCustomLang $this
     */
    public function setMenuElementCustom(MenuElementCustom $menuElementCustom): MenuElementCustomLang
    {
        $this->menuElementCustom = $menuElementCustom;

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
     * @return MenuElementCustomLang $this
     */
    public function setName(string $name): MenuElementCustomLang
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return MenuElementCustomLang $this
     */
    public function setUrl(string $url): MenuElementCustomLang
    {
        $this->url = $url;

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
     * @return MenuElementCustomLang $this
     */
    public function setLang(Lang $lang): MenuElementCustomLang
    {
        $this->lang = $lang;

        return $this;
    }
}

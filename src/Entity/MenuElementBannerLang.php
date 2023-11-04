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
class MenuElementBannerLang
{
    /**
     * @var MenuElementBanner
     *
     * @ORM\Id
     *
     * @ORM\ManyToOne(targetEntity="Oksydan\IsMainMenu\Entity\MenuElementBanner", inversedBy="menuElementBannerLangs")
     *
     * @ORM\JoinColumn(name="id_menu_element_banner", referencedColumnName="id_menu_element_banner", nullable=false)
     */
    private $menuElementBanner;

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
     * @var string
     *
     * @ORM\Column(name="alt", type="text")
     */
    private $alt;

    /**
     * @var string
     *
     * @ORM\Column(name="filename", type="text")
     */
    private $filename;

    /**
     * @return MenuElementBanner
     */
    public function getMenuElementBanner(): MenuElementBanner
    {
        return $this->menuElementBanner;
    }

    /**
     * @param MenuElementBanner $menuElementBanner
     *
     * @return MenuElementBannerLang $this
     */
    public function setMenuElementBanner(MenuElementBanner $menuElementBanner): MenuElementBannerLang
    {
        $this->menuElementBanner = $menuElementBanner;

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
     * @return MenuElementBannerLang $this
     */
    public function setName(string $name): MenuElementBannerLang
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
     * @return MenuElementBannerLang $this
     */
    public function setUrl(string $url): MenuElementBannerLang
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getAlt(): string
    {
        return $this->alt;
    }

    /**
     * @param string $alt
     *
     * @return MenuElementBannerLang $this
     */
    public function setAlt(string $alt): MenuElementBannerLang
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     *
     * @return MenuElementBannerLang $this
     */
    public function setFilename(string $filename): MenuElementBannerLang
    {
        $this->filename = $filename;

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
     * @return MenuElementBannerLang $this
     */
    public function setLang(Lang $lang): MenuElementBannerLang
    {
        $this->lang = $lang;

        return $this;
    }
}

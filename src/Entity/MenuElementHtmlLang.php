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
class MenuElementHtmlLang
{
    /**
     * @var MenuElementHtml
     *
     * @ORM\Id
     *
     * @ORM\ManyToOne(targetEntity="Oksydan\IsMainMenu\Entity\MenuElementHtml", inversedBy="menuElementHtmlLangs")
     *
     * @ORM\JoinColumn(name="id_menu_element_html", referencedColumnName="id_menu_element_html", nullable=false)
     */
    private $menuElementHtml;

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
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @return MenuElementHtml
     */
    public function getMenuElementHtml(): MenuElementHtml
    {
        return $this->menuElementHtml;
    }

    /**
     * @param MenuElementHtml $menuElementHtml
     *
     * @return MenuElementHtmlLang $this
     */
    public function setMenuElementHtml(MenuElementHtml $menuElementHtml): MenuElementHtmlLang
    {
        $this->menuElementHtml = $menuElementHtml;

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
     * @return MenuElementHtmlLang $this
     */
    public function setName(string $name): MenuElementHtmlLang
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     *
     * @return MenuElementHtmlLang $this
     */
    public function setContent(string $content): MenuElementHtmlLang
    {
        $this->content = $content;

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
     * @return MenuElementHtmlLang $this
     */
    public function setLang(Lang $lang): MenuElementHtmlLang
    {
        $this->lang = $lang;

        return $this;
    }
}

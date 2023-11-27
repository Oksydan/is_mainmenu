<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Installer;

class ModuleInstaller
{
    const HOOKS_LIST = [
        'displayTop',
        'displayMobileMenu',
        'actionFrontControllerSetMedia',
        'actionCategoryUpdate',
        'actionCategoryDelete',
        'actionProductSave',
        'actionProductDelete',
        'actionObjectCmsUpdateAfter',
        'actionObjectCmsDeleteAfter',
    ];

    private \Module $module;

    public function __construct(
        \Module $module
    ) {
        $this->module = $module;
    }

    public function install(): bool
    {
        return $this->installHooks() && $this->installDatabase();
    }

    public function uninstall(): bool
    {
        return $this->uninstallDatabase();
    }

    private function installHooks(): bool
    {
        $success = true;

        foreach (self::HOOKS_LIST as $hook) {
            if (!$this->module->registerHook($hook)) {
                $success = false;
            }
        }

        return $success;
    }

    private function installDatabase(): bool
    {
        $success = true;

        $sql = [
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'menu_element` (
                `id_menu_element` int(11) NOT NULL AUTO_INCREMENT,
                `id_parent_menu_element` int(11) DEFAULT 0,
                `active` int(1) NOT NULL DEFAULT 0,
                `display_desktop` tinyint(1) NOT NULL DEFAULT 1,
                `display_mobile` tinyint(1) NOT NULL DEFAULT 1,
                `position` int(11) NOT NULL,
                `is_root` tinyint(1) NOT NULL DEFAULT 0,
                `depth` int(11) NOT NULL,
                `type` varchar(32) NOT NULL,
                `name` varchar(256) NOT NULL,
                `css_class` varchar(512) NOT NULL,
                PRIMARY KEY (`id_menu_element`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;',
        ];

        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'menu_element_shop` (
                `id_menu_element` int(11) NOT NULL,
                `id_shop` int(11) NOT NULL
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'menu_element_custom` (
                `id_menu_element_custom` int(11) NOT NULL AUTO_INCREMENT,
                `id_menu_element` int(11) NOT NULL,
                PRIMARY KEY (`id_menu_element_custom`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'menu_element_custom_lang` (
                `id_menu_element_custom` int(11) NOT NULL,
                `id_lang` int(11) NOT NULL,
                `name` varchar(256) NOT NULL,
                `url` varchar(256) NOT NULL
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'menu_element_category` (
                `id_menu_element_category` int(11) NOT NULL AUTO_INCREMENT,
                `id_menu_element` int(11) NOT NULL,
                `id_category` int(11) NOT NULL,
                PRIMARY KEY (`id_menu_element_category`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'menu_element_category_lang` (
                `id_menu_element_category` int(11) NOT NULL,
                `id_lang` int(11) NOT NULL,
                `name` varchar(256) NOT NULL
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'menu_element_banner` (
                `id_menu_element_banner` int(11) NOT NULL AUTO_INCREMENT,
                `id_menu_element` int(11) NOT NULL,
                PRIMARY KEY (`id_menu_element_banner`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'menu_element_banner_lang` (
                `id_menu_element_banner` int(11) NOT NULL,
                `id_lang` int(11) NOT NULL,
                `name` varchar(256) NOT NULL,
                `url` varchar(256) NOT NULL,
                `filename` varchar(256),
                `alt` varchar(256) NOT NULL
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'menu_element_html` (
                `id_menu_element_html` int(11) NOT NULL AUTO_INCREMENT,
                `id_menu_element` int(11) NOT NULL,
                PRIMARY KEY (`id_menu_element_html`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'menu_element_html_lang` (
                `id_menu_element_html` int(11) NOT NULL,
                `id_lang` int(11) NOT NULL,
                `name` varchar(256) NOT NULL,
                `content` TEXT NOT NULL
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'menu_element_cms` (
                `id_menu_element_cms` int(11) NOT NULL AUTO_INCREMENT,
                `id_menu_element` int(11) NOT NULL,
                `id_cms` int(11) NOT NULL,
                PRIMARY KEY (`id_menu_element_cms`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'menu_element_cms_lang` (
                `id_menu_element_cms` int(11) NOT NULL,
                `id_lang` int(11) NOT NULL,
                `name` varchar(256) NOT NULL
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'menu_element_product` (
                `id_menu_element_product` int(11) NOT NULL AUTO_INCREMENT,
                `id_menu_element` int(11) NOT NULL,
                `id_product` int(11) NOT NULL,
                `id_product_attribute` int(11) NOT NULL,
                PRIMARY KEY (`id_menu_element_product`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        foreach ($sql as $query) {
            if (!\Db::getInstance()->execute($query)) {
                $success = false;
            }
        }

        return $success;
    }

    private function uninstallDatabase(): bool
    {
        $success = true;

        $sql = [
            'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'menu_element`',
            'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'menu_element_shop`',
            'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'menu_element_custom`',
            'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'menu_element_custom_lang`',
            'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'menu_element_category`',
            'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'menu_element_category_lang`',
            'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'menu_element_banner`',
            'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'menu_element_banner_lang`',
            'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'menu_element_html`',
            'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'menu_element_html_lang`',
            'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'menu_element_cms`',
            'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'menu_element_cms_lang`',
        ];

        foreach ($sql as $query) {
            if (!\Db::getInstance()->execute($query)) {
                $success = false;
            }
        }

        return $success;
    }
}

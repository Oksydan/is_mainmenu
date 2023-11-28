<?php

declare(strict_types=1);

if (!defined('_PS_VERSION_')) {
    exit;
}

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
} else {
    throw new \Exception('You must run "composer install --no-dev" command in module directory');
}

use Oksydan\IsMainMenu\Hook\HookInterface;
use Oksydan\IsMainMenu\Installer\ModuleInstaller;
use Oksydan\IsMainMenu\Translations\TranslationDomains;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class Is_mainmenu extends Module
{
    public $multistoreCompatibility = self::MULTISTORE_COMPATIBILITY_YES;

    public function __construct()
    {
        $this->name = 'is_mainmenu';

        $this->author = 'Igor Stępień';
        $this->version = '1.0.0';
        $this->need_instance = 0;

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->trans('Main menu module', [], TranslationDomains::TRANSLATION_DOMAIN_ADMIN);
        $this->description = $this->trans('Main menu module', [], TranslationDomains::TRANSLATION_DOMAIN_ADMIN);
        $this->controllers = ['ajax'];
        $this->ps_versions_compliancy = ['min' => '8.1.0', 'max' => _PS_VERSION_];
    }

    public function isUsingNewTranslationSystem()
    {
        return true;
    }

    private function getModuleInstaller(): ModuleInstaller
    {
        return new ModuleInstaller($this);
    }

    /**
     * @return bool
     */
    public function install(): bool
    {
        return parent::install() && $this->getModuleInstaller()->install();
    }

    /**
     * @return bool
     */
    public function uninstall(): bool
    {
        return parent::uninstall() && $this->getModuleInstaller()->uninstall();
    }

    /**
     * @template T
     *
     * @param class-string<T>|string $serviceName
     *
     * @return T|object|null
     */
    public function getService($serviceName)
    {
        try {
            return $this->get($serviceName);
        } catch (ServiceNotFoundException $exception) {
            return null;
        }
    }

    /** @param string $methodName */
    public function __call($methodName, array $arguments)
    {
        if (str_starts_with($methodName, 'hook') && $hook = $this->getHookObject($methodName)) {
            return $hook->execute(...$arguments);
        } else {
            return null;
        }
    }

    /**
     * @param string $methodName
     *
     * @return HookInterface|null
     */
    private function getHookObject($methodName)
    {
        $serviceName = sprintf(
            'Oksydan\IsMainMenu\Hook\%s',
            ucwords(str_replace('hook', '', $methodName))
        );

        $hook = $this->getService($serviceName);

        return $hook instanceof HookInterface ? $hook : null;
    }

    public function getContent(): void
    {
        \Tools::redirectAdmin(SymfonyContainer::getInstance()->get('router')->generate('is_mainmenu_controller_index'));
    }

    public function getImagePath(string $image): string
    {
        return $this->_path . 'img/' . $image;
    }

    public function getImageAbsoluteDir(string $image): string
    {
        return _PS_MODULE_DIR_ . $this->name . '/img/' . $image;
    }

    public function getCacheId($name = null)
    {
        return parent::getCacheId($name);
    }

    public function _clearCache($template, $cache_id = null, $compile_id = null)
    {
        return parent::_clearCache($template, $cache_id, $compile_id);
    }
}

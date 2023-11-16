<?php

namespace Oksydan\IsMainMenu\View\Front;

abstract class AbstractMenuRender implements MenuFrontRenderInterface
{
    protected \Is_mainmenu $module;

    protected \Context $context;

    protected string $templateFile;

    public function __construct(
        \Is_mainmenu $module,
        \Context $context
    )
    {
        $this->module = $module;
        $this->context = $context;
    }

    public function render(int $idMenuElement): string
    {
        $smartyCacheKey = $this->getCacheKey($idMenuElement);

        if (!$this->module->isCached($this->getTemplate(), $smartyCacheKey)) {
            $this->assignTemplateVariables($idMenuElement);
        }

        return $this->module->fetch($this->getTemplate(), $smartyCacheKey);
    }

    protected function getTemplate(): string
    {
        return "module:{$this->module->name}/views/templates/front/$this->templateFile";
    }

    public function clearCache()
    {
        $this->context->smarty->clearCache(
            $this->module->getTemplatePath($this->getTemplate()),
        );
    }

    abstract protected function assignTemplateVariables(int $idMenuElement): void;

    abstract protected function getCacheKey(int $idMenuElement): string;
}

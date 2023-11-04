<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Hook;

use Oksydan\IsMainMenu\Menu\MenuTree;

abstract class AbstractDisplayHook extends AbstractHook implements HookInterface
{
    /**
     * @var MenuTree
     */
    protected MenuTree $menuTree;

    public function __construct(
        \Is_mainmenu $module,
        \Context $context,
        MenuTree $menuTree
    ) {
        parent::__construct($module, $context);

        $this->menuTree = $menuTree;
    }

    public function execute(array $params): string
    {
        if (!$this->shouldBlockBeDisplayed($params)) {
            return '';
        }

        $this->assignTemplateVariables($params);

        return $this->module->fetch($this->getTemplateFullPath());
    }

    protected function assignTemplateVariables(array $params)
    {
    }

    protected function shouldBlockBeDisplayed(array $params): bool
    {
        return true;
    }

    public function getTemplateFullPath(): string
    {
        return "module:{$this->module->name}/views/templates/hook/{$this->getTemplate()}";
    }

    abstract protected function getTemplate(): string;
}

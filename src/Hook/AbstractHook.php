<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Hook;

abstract class AbstractHook implements HookInterface
{
    /**
     * @var \Is_mainmenu
     */
    protected $module;

    /**
     * @var \Context
     */
    protected $context;

    public function __construct(
        \Is_mainmenu $module,
        \Context $context
    ) {
        $this->module = $module;
        $this->context = $context;
    }
}

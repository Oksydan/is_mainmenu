<?php

namespace Oksydan\IsMainMenu\Handler\MenuElement;

interface MenuElementHandlerInterface
{
    public function handle(int $menuElementId): void;
}

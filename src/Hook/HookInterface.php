<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Hook;

interface HookInterface
{
    public function execute(array $params);
}

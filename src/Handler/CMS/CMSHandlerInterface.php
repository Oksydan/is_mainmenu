<?php

namespace Oksydan\IsMainMenu\Handler\CMS;

interface CMSHandlerInterface
{
    public function handle(\CMS $cms): void;
}

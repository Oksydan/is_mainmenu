<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Factory;

use Oksydan\IsMainMenu\Exceptions\FileEraserTypeNotExistsException;
use Oksydan\IsMainMenu\Handler\File\FileEraser;

class FileEraserFactory
{
    const IMAGE_DIR = _PS_MODULE_DIR_ . 'is_mainmenu/img/';

    public function create($fileDir): FileEraser
    {
        if (in_array($fileDir, $this->getAvailableTypes())) {
            return new FileEraser($fileDir);
        } else {
            throw new FileEraserTypeNotExistsException('File eraser type not exists.');
        }
    }

    private function getAvailableTypes(): array
    {
        return [
            self::IMAGE_DIR,
        ];
    }
}

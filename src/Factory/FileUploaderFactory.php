<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Factory;

use Oksydan\IsMainMenu\Exceptions\FileUploaderTypeNotExistsException;
use Oksydan\IsMainMenu\Handler\File\FileUploader;

class FileUploaderFactory
{
    const IMAGE_DIR = _PS_MODULE_DIR_ . 'is_mainmenu/img/';

    public function create($fileDir): FileUploader
    {
        if (in_array($fileDir, $this->getAvailableTypes())) {
            return new FileUploader($fileDir);
        } else {
            throw new FileUploaderTypeNotExistsException('File uploader type not exists.');
        }
    }

    private function getAvailableTypes(): array
    {
        return [
            self::IMAGE_DIR,
        ];
    }
}

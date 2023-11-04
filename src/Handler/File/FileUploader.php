<?php

declare(strict_types=1);

namespace Oksydan\IsMainMenu\Handler\File;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private $targetDirectory;

    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function upload(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $fileName = md5($originalFilename . time() . uniqid()) . '.' . $file->guessExtension();

        $this->createUploadDirectoryIfNotExists();

        try {
            $file->move($this->getTargetDirectory(), $fileName);
        } catch (FileException $e) {
            throw new FileException('File upload error.');
        }

        return $fileName;
    }

    private function createUploadDirectoryIfNotExists(): void
    {
        if (!file_exists($this->getTargetDirectory())) {
            mkdir($this->getTargetDirectory(), 0755, true);
        }
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
}

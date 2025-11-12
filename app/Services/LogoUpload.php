<?php

namespace App\Services;

use App\Models\Logo;
use Core\Constants\Constants;

class LogoUpload
{
    private array $image;

    public function __construct(
        private int $user_id,
        private array $validations = []
    ) {
    }

    public function upload(array $image): Logo|false
    {
        $this->image = $image;

        $logo = new Logo();
        $logo->user_id = $this->user_id;

        if (!$this->isValidImage($logo)) {
            return false;
        }

        $logo->original_name = $this->image['name'];
        $logo->file_size = $this->image['size'];
        $logo->file_extension = $this->getFileExtension();
        $logo->name = $this->generateLogoName();
        $logo->file_path = 'temp';
        $logo->uploaded_at = date('Y-m-d H:i:s');

        $dimensions = $this->getImageDimensions();
        if ($dimensions) {
            $logo->width = $dimensions['width'];
            $logo->height = $dimensions['height'];
        }

        if (!$logo->save()) {
            return false;
        }

        $logo->file_path = $this->baseDir($logo->id) . $this->getFileName();

        if ($this->uploadFile($logo)) {
            $logo->update(['file_path' => $logo->file_path]);
            return $logo;
        }

        $logo->destroy();
        return false;
    }

    private function uploadFile(Logo $logo): bool
    {
        if (empty($this->getTmpFilePath())) {
            return false;
        }

        $resp = move_uploaded_file(
            $this->getTmpFilePath(),
            $this->getAbsoluteDestinationPath($logo)
        );

        if (!$resp) {
            $error = error_get_last();
            throw new \RuntimeException(
                'Failed to move uploaded file: ' . ($error['message'] ?? 'Unknown error')
            );
        }

        return true;
    }

    private function getTmpFilePath(): string
    {
        return $this->image['tmp_name'];
    }

    private function generateLogoName(): string
    {
        $name = pathinfo($this->image['name'], PATHINFO_FILENAME);
        $timestamp = time();
        return "{$name}_{$timestamp}";
    }

    private function getFileName(): string
    {
        $file_extension = $this->getFileExtension();
        $name = pathinfo($this->image['name'], PATHINFO_FILENAME);
        $timestamp = time();
        return "{$name}_{$timestamp}.{$file_extension}";
    }

    private function getFileExtension(): string
    {
        $file_name_splitted = explode('.', $this->image['name']);
        return strtolower(end($file_name_splitted));
    }

    private function getAbsoluteDestinationPath(Logo $logo): string
    {
        return $this->storeDir($logo->id) . $this->getFileName();
    }

    private function baseDir(int $logo_id): string
    {
        return "/assets/uploads/logos/{$this->user_id}/{$logo_id}/";
    }

    private function storeDir(int $logo_id): string
    {
        $path = Constants::rootPath()->join('public' . $this->baseDir($logo_id));
        if (!is_dir($path)) {
            mkdir(directory: $path, recursive: true);
        }

        return $path;
    }

    private function getImageDimensions(): ?array
    {
        $valid_image_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if (in_array($this->image['type'], $valid_image_types)) {
            $size = getimagesize($this->getTmpFilePath());
            if ($size) {
                return [
                    'width' => $size[0],
                    'height' => $size[1]
                ];
            }
        }

        return null;
    }

    private function isValidImage(Logo $logo): bool
    {
        if (isset($this->validations['extension'])) {
            $this->validateImageExtension($logo);
        }

        if (isset($this->validations['size'])) {
            $this->validateImageSize($logo);
        }

        return $logo->errors('logo') === null;
    }

    private function validateImageExtension(Logo $logo): void
    {
        $file_extension = $this->getFileExtension();

        if (!in_array($file_extension, $this->validations['extension'])) {
            $logo->addError('logo', 'Extensão de arquivo inválida');
        }
    }

    private function validateImageSize(Logo $logo): void
    {
        if ($this->image['size'] > $this->validations['size']) {
            $logo->addError('logo', 'Tamanho do arquivo inválido');
        }
    }

    public function delete(Logo $logo): bool
    {
        $file_path = Constants::rootPath()->join('public' . $logo->file_path);

        if (file_exists($file_path)) {
            unlink($file_path);

            $dir = dirname($file_path);
            if (is_dir($dir) && count(scandir($dir)) == 2) {
                rmdir($dir);
            }
        }

        return $logo->destroy();
    }
}

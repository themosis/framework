<?php

namespace Themosis\Core\Maintenance;

use Illuminate\Contracts\Foundation\MaintenanceMode;

class WordPressMaintenanceMode implements MaintenanceMode
{
    public function __construct(protected string $filePath)
    {
    }

    public function activate(array $payload): void
    {
        file_put_contents($this->filePath, json_encode($payload));
    }

    public function deactivate(): void
    {
        if (file_exists($this->filePath)) {
            unlink($this->filePath);
        }
    }

    public function active(): bool
    {
        return file_exists($this->filePath);
    }

    public function data(): array
    {
        if (! file_exists($this->filePath)) {
            return [];
        }

        $data = json_decode(file_get_contents($this->filePath), true);

        return is_array($data) ? $data : [];
    }
}

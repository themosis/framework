<?php

namespace Themosis\Core\Maintenance;

use Illuminate\Contracts\Foundation\MaintenanceMode;

/**
 * Maintenance mode backed by the WordPress ".maintenance" file.
 *
 * WordPress includes ".maintenance" as PHP, so it only ever receives a
 * `$upgrading` assignment. The Laravel payload (retry, secret, status...)
 * is stored separately as JSON.
 */
class WordPressMaintenanceMode implements MaintenanceMode
{
    public function __construct(
        protected string $maintenanceFile,
        protected string $payloadFile,
    ) {
    }

    /**
     * Take the application down for maintenance.
     */
    public function activate(array $payload): void
    {
        // A literal time() keeps WordPress in maintenance until deactivated.
        file_put_contents($this->maintenanceFile, '<?php $upgrading = time(); ?>');

        if (is_dir(dirname($this->payloadFile))) {
            file_put_contents($this->payloadFile, json_encode($payload, JSON_PRETTY_PRINT));
        }
    }

    /**
     * Take the application out of maintenance.
     */
    public function deactivate(): void
    {
        foreach ([$this->maintenanceFile, $this->payloadFile] as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    /**
     * Determine if the application is currently down for maintenance.
     */
    public function active(): bool
    {
        return is_file($this->maintenanceFile);
    }

    /**
     * Get the data array which was provided when the application was placed into maintenance.
     */
    public function data(): array
    {
        if (! $this->active() || ! is_file($this->payloadFile)) {
            return [];
        }

        $data = json_decode((string) file_get_contents($this->payloadFile), true);

        return is_array($data) ? $data : [];
    }
}

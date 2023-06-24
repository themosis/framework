<?php

namespace Themosis\View;

class FileViewLocation
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var int
     */
    private $priority;

    public function __construct(string $path, int $priority)
    {
        $this->path = $path;
        $this->priority = $priority;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }
}

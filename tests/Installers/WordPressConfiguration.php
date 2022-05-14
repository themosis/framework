<?php

namespace Themosis\Tests\Installers;

class WordPressConfiguration
{
    private string $tablePrefix;

    private string $blogTitle;

    private string $username;

    private ?string $password = null;

    private string $email;

    private bool $isPublic = false;

    private function __construct()
    {
    }

    public static function make(): self
    {
        $self = new self();
        $self->tablePrefix = 'wp_';
        $self->blogTitle = 'Themosis Tests';
        $self->username = 'themosis';
        $self->email = 'fake@themosis.com';

        return $self;
    }

    public function tablePrefix(): string
    {
        return $this->tablePrefix;
    }

    public function blogTitle(): string
    {
        return $this->blogTitle;
    }

    public function username(): string
    {
        return $this->username;
    }

    public function password(): ?string
    {
        return $this->password;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function isPublic(): bool
    {
        return $this->isPublic;
    }
}

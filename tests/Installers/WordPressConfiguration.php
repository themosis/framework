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

    private string $siteUrl;

    private string $homeUrl;

    private string $databaseName;

    private string $databaseUser;

    private string $databasePassword;

    private string $databaseHost;

    private string $databaseCharset;

    private string $databaseCollate;

    private string $defaultTheme;

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
        $self->siteUrl = 'https://themosis.test';
        $self->homeUrl = 'https://themosis.test';
        $self->defaultTheme = 'themosis-fake-theme';

        $self->databaseName = getenv('TEST_DATABASE_NAME') ?: 'themosis_tests';
        $self->databaseUser = getenv('TEST_DATABASE_USER') ?: 'root';
        $self->databasePassword = getenv('TEST_DATABASE_PASSWORD') ?: 'root';
        $self->databaseHost = getenv('TEST_DATABASE_HOST') ?: 'localhost';
        $self->databaseCharset = getenv('TEST_DATABASE_CHARSET') ?: 'utf8mb4';
        $self->databaseCollate = getenv('TEST_DATABASE_COLLATE') ?: 'utf8mb4_unicode_ci';

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

    public function siteUrl(): string
    {
        return $this->siteUrl;
    }

    public function homeUrl(): string
    {
        return $this->homeUrl;
    }

    public function databaseName(): string
    {
        return $this->databaseName;
    }

    public function databaseUser(): string
    {
        return $this->databaseUser;
    }

    public function databasePassword(): string
    {
        return $this->databasePassword;
    }

    public function databaseHost(): string
    {
        return $this->databaseHost;
    }

    public function databaseCharset(): string
    {
        return $this->databaseCharset;
    }

    public function databaseCollate(): string
    {
        return $this->databaseCollate;
    }

    public function defaultTheme(): string
    {
        return $this->defaultTheme;
    }

    public function withTablePrefix(string $prefix): self
    {
        $this->tablePrefix = $prefix;

        return $this;
    }

    public function withBlogTitle(string $title): self
    {
        $this->blogTitle = $title;

        return $this;
    }

    public function withUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function withPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function withEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function setIsPublic(bool $isPublic): self
    {
        $this->isPublic = $isPublic;

        return $this;
    }

    public function withSiteUrl(string $siteUrl): self
    {
        $this->siteUrl = $siteUrl;

        return $this;
    }

    public function withHomeUrl(string $homeUrl): self
    {
        $this->homeUrl = $homeUrl;

        return $this;
    }

    public function withDatabaseName(string $databaseName): self
    {
        $this->databaseName = $databaseName;

        return $this;
    }

    public function withDatabaseUser(string $user): self
    {
        $this->databaseUser = $user;

        return $this;
    }

    public function withDatabasePassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function withDatabaseHost(string $host): self
    {
        $this->databaseHost = $host;

        return $this;
    }

    public function withDatabaseCharset(string $charset): self
    {
        $this->databaseCharset = $charset;

        return $this;
    }

    public function withDatabaseCollate(string $collate): self
    {
        $this->databaseCollate = $collate;

        return $this;
    }
}

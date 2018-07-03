<?php

namespace Themosis\Page;

use Themosis\Page\Contracts\SettingsRepositoryInterface;

class PageSettingsRepository implements SettingsRepositoryInterface
{
    /**
     * @var array
     */
    protected $sections = [];

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * Set the page repository sections.
     *
     * @param array $sections
     *
     * @return SettingsRepositoryInterface
     */
    public function setSections(array $sections): SettingsRepositoryInterface
    {
        $this->sections = $sections;

        return $this;
    }

    /**
     * Return the page repository sections.
     *
     * @return array
     */
    public function getSections(): array
    {
        return $this->sections;
    }

    /**
     * Set the page repository settings.
     *
     * @param array $settings
     *
     * @return SettingsRepositoryInterface
     */
    public function setSettings(array $settings): SettingsRepositoryInterface
    {
        $this->settings = $settings;

        return $this;
    }

    /**
     * Return the page repository settings.
     *
     * @return array
     */
    public function getSettings(): array
    {
        return $this->settings;
    }
}

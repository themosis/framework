<?php

namespace Themosis\Page;

use Illuminate\Support\Collection;
use Themosis\Forms\Contracts\FieldTypeInterface;
use Themosis\Forms\Fields\Contracts\CanHandlePageSettings;
use Themosis\Page\Contracts\SettingsRepositoryInterface;
use Themosis\Support\Contracts\SectionInterface;

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
    public function getSections(): Collection
    {
        return (new Collection($this->sections));
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
        $this->parseCompatibleSettings($settings);
        $this->settings = $settings;

        return $this;
    }

    /**
     * Return the page repository settings.
     *
     * @return array
     */
    public function getSettings(): Collection
    {
        return (new Collection($this->settings));
    }

    /**
     * Return the setting instance based on its name.
     *
     * @param string $name
     *
     * @return FieldTypeInterface
     */
    public function getSettingByName(string $name): FieldTypeInterface
    {
        return $this->getSettings()->collapse()->first(function ($setting) use ($name) {
            /** @var FieldTypeInterface $setting */
            return $name === $setting->getName();
        });
    }

    /**
     * Return the section instance based on its name.
     *
     * @param string $name
     *
     * @return SectionInterface
     */
    public function getSectionByName(string $name): SectionInterface
    {
        return $this->getSections()->first(function ($section) use ($name) {
            /** @var SectionInterface $section */
            return $name === $section->getId();
        });
    }

    /**
     * Parse page settings.
     * Throw an exception if user is using a field not supported.
     *
     * @param array $settings
     *
     * @throws \Exception
     */
    protected function parseCompatibleSettings(array $settings)
    {
        $settings = collect($settings)->collapse()->all();

        foreach ($settings as $setting) {
            if ($setting instanceof CanHandlePageSettings) {
                $setting->settingGet();
            }
        }
    }
}

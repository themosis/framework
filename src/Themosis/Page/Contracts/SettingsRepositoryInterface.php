<?php

namespace Themosis\Page\Contracts;

use Illuminate\Support\Collection;
use Themosis\Forms\Contracts\FieldTypeInterface;
use Themosis\Support\Contracts\SectionInterface;

interface SettingsRepositoryInterface
{
    /**
     * Set sections of the repository.
     *
     * @param array $sections
     *
     * @return SettingsRepositoryInterface
     */
    public function setSections(array $sections): SettingsRepositoryInterface;

    /**
     * Return the page repository sections.
     *
     * @return array
     */
    public function getSections(): Collection;

    /**
     * Set the repository settings.
     *
     * @param array $settings
     *
     * @return SettingsRepositoryInterface
     */
    public function setSettings(array $settings): SettingsRepositoryInterface;

    /**
     * Return the repository settings.
     *
     * @return array
     */
    public function getSettings(): Collection;

    /**
     * Return the setting instance.
     *
     * @param string $name
     *
     * @return FieldTypeInterface
     */
    public function getSettingByName(string $name): FieldTypeInterface;

    /**
     * Return the section instance.
     *
     * @param string $name
     *
     * @return SectionInterface
     */
    public function getSectionByName(string $name): SectionInterface;
}

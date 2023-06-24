<?php

namespace Themosis\Page\Contracts;

use Illuminate\Support\Collection;
use Themosis\Forms\Contracts\FieldTypeInterface;
use Themosis\Support\Contracts\SectionInterface;

interface SettingsRepositoryInterface
{
    /**
     * Set sections of the repository.
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
     */
    public function getSettingByName(string $name): FieldTypeInterface;

    /**
     * Return the section instance.
     */
    public function getSectionByName(string $name): SectionInterface;
}

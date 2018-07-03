<?php

namespace Themosis\Page\Contracts;

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
    public function getSections(): array;

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
    public function getSettings(): array;
}

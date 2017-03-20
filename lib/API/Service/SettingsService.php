<?php

namespace Netgen\BlockManager\API\Service;

interface SettingsService extends Service
{
    /**
     * Loads settings for provided entity and settings identifier.
     *
     * @param mixed $entity
     * @param string $settingsIdentifier
     *
     * @return mixed
     */
    public function loadSettings($entity, $settingsIdentifier);

    /**
     * Loads all settings for provided entity.
     *
     * @param mixed $entity
     *
     * @return mixed
     */
    public function loadAllSettings($entity);
}

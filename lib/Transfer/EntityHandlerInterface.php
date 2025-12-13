<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer;

use Netgen\Layouts\API\Values\Value;
use Symfony\Component\Uid\Uuid;

interface EntityHandlerInterface
{
    /**
     * Loads the entity with provided UUID.
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException if the layout with provided UUID does not exist
     */
    public function loadEntity(Uuid $uuid): Value;

    /**
     * Returns if the entity with provided UUID exists.
     */
    public function entityExists(Uuid $uuid): bool;

    /**
     * Remove the entity with provided UUID.
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException if the layout with provided UUID does not exist
     */
    public function deleteEntity(Uuid $uuid): void;

    /**
     * Imports an entity from the given serialized $data.
     *
     * @param array<string, mixed> $data
     */
    public function importEntity(array $data, bool $keepUuid): Value;
}

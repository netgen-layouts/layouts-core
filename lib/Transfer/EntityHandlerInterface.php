<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer;

use Netgen\Layouts\API\Values\Value;
use Ramsey\Uuid\UuidInterface;

interface EntityHandlerInterface
{
    /**
     * Loads the entity with provided UUID.
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException if the layout with provided UUID does not exist
     */
    public function loadEntity(UuidInterface $uuid): Value;

    /**
     * Returns if the entity with provided UUID exists.
     */
    public function entityExists(UuidInterface $uuid): bool;

    /**
     * Remove the entity with provided UUID.
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException if the layout with provided UUID does not exist
     */
    public function deleteEntity(UuidInterface $uuid): void;

    /**
     * Imports an entity from the given serialized $data.
     *
     * @param array<string, mixed> $data
     */
    public function importEntity(array $data, bool $keepUuid): Value;
}

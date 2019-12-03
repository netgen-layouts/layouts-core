<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Input\Result;

use Ramsey\Uuid\UuidInterface;

interface ResultInterface
{
    /**
     * Returns the entity type which was being imported.
     */
    public function getEntityType(): string;

    /**
     * Returns the data which was being imported.
     *
     * @return array<string, mixed>
     */
    public function getData(): array;

    /**
     * Returns the UUID of the entity which was imported.
     */
    public function getEntityId(): UuidInterface;
}

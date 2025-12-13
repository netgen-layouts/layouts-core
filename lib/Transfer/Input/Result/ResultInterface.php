<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Input\Result;

use Symfony\Component\Uid\Uuid;

interface ResultInterface
{
    /**
     * Returns the entity type which was being imported.
     */
    public string $entityType { get; }

    /**
     * Returns the data which was being imported.
     *
     * @var array<string, mixed>
     */
    public array $data { get; }

    /**
     * Returns the UUID of the entity which was imported.
     */
    public Uuid $entityId { get; }
}

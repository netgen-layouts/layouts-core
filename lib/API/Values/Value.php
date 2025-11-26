<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values;

use Ramsey\Uuid\UuidInterface;

interface Value
{
    /**
     * Returns the value UUID.
     */
    public UuidInterface $id { get; }

    /**
     * Returns the status of the value.
     *
     * A value can have one of three statuses: draft, published or archived.
     */
    public Status $status { get; }

    /**
     * Returns if the value is a draft.
     */
    public function isDraft(): bool;

    /**
     * Returns if the value is published.
     */
    public function isPublished(): bool;

    /**
     * Returns if the value is archived.
     */
    public function isArchived(): bool;
}

<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values;

use Ramsey\Uuid\UuidInterface;

interface Value
{
    public const STATUS_DRAFT = 0;

    public const STATUS_PUBLISHED = 1;

    public const STATUS_ARCHIVED = 2;

    /**
     * Returns the value UUID.
     */
    public function getId(): UuidInterface;

    /**
     * Returns the status of the value.
     *
     * A value can have one of three statuses: draft, published or archived.
     */
    public function getStatus(): int;

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

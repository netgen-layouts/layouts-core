<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\Collection;

use Netgen\Layouts\Utils\HydratorTrait;

final class CollectionUpdateStruct
{
    use HydratorTrait;

    /**
     * Starting offset for the collection results.
     */
    public ?int $offset = null;

    /**
     * Starting limit for the collection results.
     *
     * Set to 0 to disable the limit.
     */
    public ?int $limit = null;

    /**
     * Flag indicating if the collection will be always available.
     */
    public ?bool $alwaysAvailable = null;

    /**
     * Flag indicating if the collection will be translatable.
     */
    public ?bool $isTranslatable = null;
}

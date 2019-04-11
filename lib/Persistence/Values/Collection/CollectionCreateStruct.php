<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\Collection;

use Netgen\Layouts\Utils\HydratorTrait;

final class CollectionCreateStruct
{
    use HydratorTrait;

    /**
     * Status of the new collection.
     *
     * @var int
     */
    public $status;

    /**
     * Starting offset for the collection results.
     *
     * @var int
     */
    public $offset;

    /**
     * Starting limit for the collection results.
     *
     * @var int|null
     */
    public $limit;

    /**
     * Flag indicating if the collection will be always available.
     *
     * @var bool
     */
    public $alwaysAvailable;

    /**
     * Flag indicating if the collection will be translatable.
     *
     * @var bool
     */
    public $isTranslatable;

    /**
     * Main locale of the new collection.
     *
     * @var string
     */
    public $mainLocale;
}

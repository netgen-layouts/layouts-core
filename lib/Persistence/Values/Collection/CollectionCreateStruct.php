<?php

namespace Netgen\BlockManager\Persistence\Values\Collection;

use Netgen\BlockManager\Value;

final class CollectionCreateStruct extends Value
{
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
     * @var int
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

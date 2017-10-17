<?php

namespace Netgen\BlockManager\Persistence\Values\Collection;

use Netgen\BlockManager\ValueObject;

final class CollectionUpdateStruct extends ValueObject
{
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
}

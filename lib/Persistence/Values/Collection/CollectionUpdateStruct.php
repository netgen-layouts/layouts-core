<?php

namespace Netgen\BlockManager\Persistence\Values\Collection;

use Netgen\BlockManager\Value;

final class CollectionUpdateStruct extends Value
{
    /**
     * Starting offset for the collection results.
     *
     * @var int|null
     */
    public $offset;

    /**
     * Starting limit for the collection results.
     *
     * Set to 0 to disable the limit.
     *
     * @var int|null
     */
    public $limit;

    /**
     * Flag indicating if the collection will be always available.
     *
     * @var bool|null
     */
    public $alwaysAvailable;

    /**
     * Flag indicating if the collection will be translatable.
     *
     * @var bool|null
     */
    public $isTranslatable;
}

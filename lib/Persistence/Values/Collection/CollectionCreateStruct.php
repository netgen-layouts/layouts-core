<?php

namespace Netgen\BlockManager\Persistence\Values\Collection;

use Netgen\BlockManager\ValueObject;

final class CollectionCreateStruct extends ValueObject
{
    /**
     * Status of the new collection.
     *
     * @var int
     */
    public $status;

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

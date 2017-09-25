<?php

namespace Netgen\BlockManager\Persistence\Values\Collection;

use Netgen\BlockManager\ValueObject;

final class CollectionUpdateStruct extends ValueObject
{
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

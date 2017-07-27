<?php

namespace Netgen\BlockManager\Persistence\Values\Collection;

use Netgen\BlockManager\ValueObject;

class CollectionUpdateStruct extends ValueObject
{
    /**
     * @var bool
     */
    public $alwaysAvailable;

    /**
     * @var bool
     */
    public $isTranslatable;
}

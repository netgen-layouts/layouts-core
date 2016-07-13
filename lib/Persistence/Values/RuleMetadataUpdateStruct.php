<?php

namespace Netgen\BlockManager\Persistence\Values;

use Netgen\BlockManager\ValueObject;

class RuleMetadataUpdateStruct extends ValueObject
{
    /**
     * @var bool
     */
    public $enabled;

    /**
     * @var int
     */
    public $priority;
}

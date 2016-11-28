<?php

namespace Netgen\BlockManager\Persistence\Values\LayoutResolver;

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

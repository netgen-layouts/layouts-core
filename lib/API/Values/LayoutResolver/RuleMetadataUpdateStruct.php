<?php

namespace Netgen\BlockManager\API\Values\LayoutResolver;

use Netgen\BlockManager\ValueObject;

final class RuleMetadataUpdateStruct extends ValueObject
{
    /**
     * New priority of the layout.
     *
     * @var int
     */
    public $priority;
}

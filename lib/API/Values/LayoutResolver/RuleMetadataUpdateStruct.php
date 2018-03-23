<?php

namespace Netgen\BlockManager\API\Values\LayoutResolver;

use Netgen\BlockManager\Value;

final class RuleMetadataUpdateStruct extends Value
{
    /**
     * New priority of the layout.
     *
     * @var int
     */
    public $priority;
}

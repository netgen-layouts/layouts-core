<?php

namespace Netgen\BlockManager\Persistence\Values\LayoutResolver;

use Netgen\BlockManager\ValueObject;

class RuleUpdateStruct extends ValueObject
{
    /**
     * ID of the mapped layout. Set to 0 to remove the existing mapping.
     *
     * @var int|string
     */
    public $layoutId;

    /**
     * Human readable comment of the rule.
     *
     * @var string
     */
    public $comment;
}

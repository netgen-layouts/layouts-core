<?php

namespace Netgen\BlockManager\API\Values\LayoutResolver;

use Netgen\BlockManager\ValueObject;

final class RuleUpdateStruct extends ValueObject
{
    /**
     * The ID of the layout to which the rule will be linked.
     *
     * Set to 0 to remove the mapping.
     *
     * @var int|string
     */
    public $layoutId;

    /**
     * Description of the rule.
     *
     * @var string
     */
    public $comment;
}

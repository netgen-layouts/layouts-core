<?php

namespace Netgen\BlockManager\API\Values\LayoutResolver;

use Netgen\BlockManager\ValueObject;

final class RuleCreateStruct extends ValueObject
{
    /**
     * The ID of the layout to which the rule will be mapped.
     *
     * @var int|string
     */
    public $layoutId;

    /**
     * Priority of the rule.
     *
     * @var int
     */
    public $priority = 0;

    /**
     * Specifies if the rule will be enabled or not.
     *
     * @var bool
     */
    public $enabled = false;

    /**
     * Description of the rule.
     *
     * @var string
     */
    public $comment;
}

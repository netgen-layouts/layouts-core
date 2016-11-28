<?php

namespace Netgen\BlockManager\Persistence\Values\LayoutResolver;

use Netgen\BlockManager\ValueObject;

class RuleUpdateStruct extends ValueObject
{
    /**
     * @var int|string
     */
    public $layoutId;

    /**
     * @var string
     */
    public $comment;
}

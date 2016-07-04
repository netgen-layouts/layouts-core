<?php

namespace Netgen\BlockManager\Persistence\Values\LayoutResolver;

use Netgen\BlockManager\ValueObject;

class Condition extends ValueObject
{
    /**
     * @var int|string
     */
    public $id;

    /**
     * @var int
     */
    public $status;

    /**
     * @var int|string
     */
    public $ruleId;

    /**
     * @var string
     */
    public $type;

    /**
     * @var mixed
     */
    public $value;
}

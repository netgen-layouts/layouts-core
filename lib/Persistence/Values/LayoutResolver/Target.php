<?php

namespace Netgen\BlockManager\Persistence\Values\LayoutResolver;

use Netgen\BlockManager\Persistence\Values\Value;

class Target extends Value
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

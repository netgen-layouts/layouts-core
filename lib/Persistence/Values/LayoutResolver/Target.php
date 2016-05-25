<?php

namespace Netgen\BlockManager\Persistence\Values\LayoutResolver;

use Netgen\BlockManager\ValueObject;

class Target extends ValueObject
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
    public $identifier;

    /**
     * @var mixed
     */
    public $value;
}

<?php

namespace Netgen\BlockManager\Tests\Core\Stubs;

use Netgen\BlockManager\API\Values\AbstractValue;
use Netgen\BlockManager\API\Values\Value as APIValue;

class Value extends AbstractValue implements APIValue
{
    /**
     * @var int
     */
    public $status;

    /**
     * @var mixed
     */
    public $someProperty;

    /**
     * @var mixed
     */
    public $someOtherProperty;
}

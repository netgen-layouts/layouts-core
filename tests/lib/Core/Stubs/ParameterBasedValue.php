<?php

namespace Netgen\BlockManager\Tests\Core\Stubs;

use Netgen\BlockManager\API\Values\ParameterBasedValue as APIParameterBasedValue;
use Netgen\BlockManager\Core\Values\ParameterBasedValueTrait;
use Netgen\BlockManager\ValueObject;

class ParameterBasedValue extends ValueObject implements APIParameterBasedValue
{
    use ParameterBasedValueTrait;
}

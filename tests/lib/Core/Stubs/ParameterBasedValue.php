<?php

namespace Netgen\BlockManager\Tests\Core\Stubs;

use Netgen\BlockManager\API\Values\ParameterBasedValue as APIParameterBasedValue;
use Netgen\BlockManager\Core\Values\ParameterBasedValueTrait;
use Netgen\BlockManager\Value;

final class ParameterBasedValue extends Value implements APIParameterBasedValue
{
    use ParameterBasedValueTrait;
}

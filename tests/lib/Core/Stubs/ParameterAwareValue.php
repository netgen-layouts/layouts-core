<?php

namespace Netgen\BlockManager\Tests\Core\Stubs;

use Netgen\BlockManager\API\Values\ParameterAwareValue as APIParameterAwareValue;
use Netgen\BlockManager\Core\Values\ParameterAwareValueTrait;
use Netgen\BlockManager\ValueObject;

class ParameterAwareValue extends ValueObject implements APIParameterAwareValue
{
    use ParameterAwareValueTrait;
}

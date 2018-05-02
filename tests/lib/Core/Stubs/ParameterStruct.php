<?php

namespace Netgen\BlockManager\Tests\Core\Stubs;

use Netgen\BlockManager\API\Values\ParameterStruct as APIParameterStruct;
use Netgen\BlockManager\API\Values\ParameterStructTrait;
use Netgen\BlockManager\Value as BaseValue;

final class ParameterStruct extends BaseValue implements APIParameterStruct
{
    use ParameterStructTrait;
}

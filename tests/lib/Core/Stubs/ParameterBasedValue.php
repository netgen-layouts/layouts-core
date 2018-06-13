<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Stubs;

use Netgen\BlockManager\API\Values\ParameterBasedValue as APIParameterBasedValue;
use Netgen\BlockManager\Core\Values\ParameterBasedValueTrait;
use Netgen\BlockManager\Value as BaseValue;

final class ParameterBasedValue extends BaseValue implements APIParameterBasedValue
{
    use ParameterBasedValueTrait;
}

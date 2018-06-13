<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters\Stubs;

use Netgen\BlockManager\API\Values\ParameterStruct as ParameterStructInterface;
use Netgen\BlockManager\API\Values\ParameterStructTrait;

final class ParameterStruct implements ParameterStructInterface
{
    use ParameterStructTrait;
}

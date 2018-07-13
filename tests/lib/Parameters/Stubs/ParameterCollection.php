<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters\Stubs;

use Netgen\BlockManager\Parameters\ParameterCollectionInterface;
use Netgen\BlockManager\Parameters\ParameterCollectionTrait;
use Netgen\BlockManager\Value as BaseValue;

final class ParameterCollection extends BaseValue implements ParameterCollectionInterface
{
    use ParameterCollectionTrait;
}

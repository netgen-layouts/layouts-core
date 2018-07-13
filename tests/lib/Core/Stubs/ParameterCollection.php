<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Stubs;

use Netgen\BlockManager\API\Values\ParameterCollection as APIParameterCollection;
use Netgen\BlockManager\Core\Values\ParameterCollectionTrait;
use Netgen\BlockManager\Value as BaseValue;

final class ParameterCollection extends BaseValue implements APIParameterCollection
{
    use ParameterCollectionTrait;
}

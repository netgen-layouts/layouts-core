<?php

namespace Netgen\BlockManager\Tests\Parameters\Stubs;

use Netgen\BlockManager\Parameters\ParameterCollectionInterface;
use Netgen\BlockManager\Parameters\ParameterCollectionTrait;

final class ParameterCollection implements ParameterCollectionInterface
{
    use ParameterCollectionTrait;

    public function __construct(array $parameterDefinitions = [])
    {
        $this->parameterDefinitions = $parameterDefinitions;
    }
}

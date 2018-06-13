<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters\Stubs;

use Netgen\BlockManager\Parameters\ParameterDefinitionCollectionInterface;
use Netgen\BlockManager\Parameters\ParameterDefinitionCollectionTrait;

final class ParameterDefinitionCollection implements ParameterDefinitionCollectionInterface
{
    use ParameterDefinitionCollectionTrait;

    public function __construct(array $parameterDefinitions = [])
    {
        $this->parameterDefinitions = $parameterDefinitions;
    }
}

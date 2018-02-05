<?php

namespace Netgen\BlockManager\Tests\Parameters\Stubs;

use Netgen\BlockManager\Parameters\CompoundParameterDefinitionInterface;
use Netgen\BlockManager\Parameters\ParameterCollectionTrait;

final class CompoundParameterDefinition extends ParameterDefinition implements CompoundParameterDefinitionInterface
{
    use ParameterCollectionTrait;

    /**
     * Sets the parameter definitions.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterDefinitionInterface[] $parameterDefinitions
     */
    public function setParameterDefinitions(array $parameterDefinitions)
    {
        $this->parameterDefinitions = $parameterDefinitions;
    }
}

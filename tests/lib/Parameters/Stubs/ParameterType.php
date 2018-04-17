<?php

namespace Netgen\BlockManager\Tests\Parameters\Stubs;

use Netgen\BlockManager\Parameters\ParameterDefinitionInterface;
use Netgen\BlockManager\Parameters\ParameterType as BaseParameterType;
use Symfony\Component\Validator\Constraints\NotNull;

final class ParameterType extends BaseParameterType
{
    public function getIdentifier()
    {
        return 'type';
    }

    protected function getValueConstraints(ParameterDefinitionInterface $parameterDefinition, $value)
    {
        return [new NotNull()];
    }
}

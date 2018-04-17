<?php

namespace Netgen\BlockManager\Tests\Parameters\Stubs;

use Netgen\BlockManager\Parameters\CompoundParameterType as BaseCompoundParameterType;
use Netgen\BlockManager\Parameters\ParameterDefinitionInterface;
use Symfony\Component\Validator\Constraints\NotNull;

final class CompoundParameterType extends BaseCompoundParameterType
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

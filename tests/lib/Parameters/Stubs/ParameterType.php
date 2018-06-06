<?php

namespace Netgen\BlockManager\Tests\Parameters\Stubs;

use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType as BaseParameterType;
use Symfony\Component\Validator\Constraints\NotNull;

final class ParameterType extends BaseParameterType
{
    public function getIdentifier()
    {
        return 'type';
    }

    public function export(ParameterDefinition $parameterDefinition, $value)
    {
        return 'export_value';
    }

    protected function getValueConstraints(ParameterDefinition $parameterDefinition, $value)
    {
        return [new NotNull()];
    }
}

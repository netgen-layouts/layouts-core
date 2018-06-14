<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters\Stubs;

use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType as BaseParameterType;
use Symfony\Component\Validator\Constraints\NotNull;

final class ParameterType extends BaseParameterType
{
    public function getIdentifier(): string
    {
        return 'type';
    }

    protected function getValueConstraints(ParameterDefinition $parameterDefinition, $value): array
    {
        return [new NotNull()];
    }
}

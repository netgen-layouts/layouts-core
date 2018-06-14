<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters\Stubs;

use Netgen\BlockManager\Parameters\CompoundParameterType as BaseCompoundParameterType;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Symfony\Component\Validator\Constraints\NotNull;

final class CompoundParameterType extends BaseCompoundParameterType
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

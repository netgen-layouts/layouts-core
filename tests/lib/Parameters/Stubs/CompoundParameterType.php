<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\Stubs;

use Netgen\Layouts\Parameters\CompoundParameterType as BaseCompoundParameterType;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Symfony\Component\Validator\Constraints\NotNull;

final class CompoundParameterType extends BaseCompoundParameterType
{
    public static function getIdentifier(): string
    {
        return 'type';
    }

    protected function getValueConstraints(ParameterDefinition $parameterDefinition, mixed $value): array
    {
        return [new NotNull()];
    }
}

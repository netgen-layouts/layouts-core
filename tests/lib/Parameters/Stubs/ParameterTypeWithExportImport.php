<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters\Stubs;

use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType as BaseParameterType;
use Symfony\Component\Validator\Constraints\NotNull;

final class ParameterTypeWithExportImport extends BaseParameterType
{
    public static function getIdentifier(): string
    {
        return 'type';
    }

    public function import(ParameterDefinition $parameterDefinition, $value)
    {
        return 'import_value';
    }

    public function export(ParameterDefinition $parameterDefinition, $value)
    {
        return 'export_value';
    }

    protected function getValueConstraints(ParameterDefinition $parameterDefinition, $value): array
    {
        return [new NotNull()];
    }
}
